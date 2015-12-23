function SFNS()
{
	SFNS.vinfo={time:'Tue Apr 26 05:52:33 UTC+0800 2011',version:'1.0',ov:'0.1.20080518'};
	var _OBS_Password='NbnpAYW_EcPfR6KwBozyA3ywQ7HqS2W_EdCfFpelBoW_EcnlOs5iQ6zpT7meFpenCZTSBZ1SBZ1SBZ4fAIaf'; function _OBS_4(obj,property)
	{
		for(var key in property)
		{
			obj[key]=property[key];
		}
	} function _OBS_3(str,password)
	{
		password=(password===false)?password:_OBS_Password;
		var passIndex,passLength;
		if(password)
		{
			passIndex=0;
			passLength=password.length;
		}
		var num=0,byt=0;
		var len=str.length;
		var resultStr=new String();
		var preNum=-1;
		var preIndex=0;
		for(var i=0;i<len;i++)
		{
			var code=str.charCodeAt(i);
			code=(code==95)?63:((code==44)?62:((code>=97)?(code-61):((code>=65)?(code-55):(code-48))));
			if(password)
			{
				code=(code-password.charCodeAt(passIndex++)+128)%64;
				passIndex=passIndex%passLength;
			}

			byt=(byt<<6)+code;
			num+=6;
			while(num>=8)
			{
				var b=byt>>(num-8);
				if(preIndex>0)
				{
					preNum=(preNum<<6)+(b&(0x3f));
					preIndex--;
					if(preIndex==0){resultStr+=String.fromCharCode(preNum);}
				}
				else
				{
					if(b>=224)
					{
						preNum=b&(0xf);
						preIndex=2;
					}
					else if(b>=128)
					{
						preNum=b&(0x1f);
						preIndex=1;
					}
					else
					{
						resultStr+=String.fromCharCode(b);
					}
				}
				byt=byt-(b<<(num-8));
				num-=8;
			}
		}
		return resultStr;
	}	var _OBS_1=[_OBS_3('ivJfVtjOod5pBVz_RrDVcjD'),'inset 1px #000000','#FFFFE1','Type,FromTask,ToTask','name,BaselineStart,BaselineFinish','name,ActualStart,ActualFinish,ActualDuration,PercentComplete','name,Start,Finish,Duration','solid 1px #FF0000','#0000FF','grid_1','solid 1px #0000FF','#F4F4F4','LineHeight,Selected','Collapse,LineHeight,Selected',"task_head_3_hollow",'Percent',"position:absolute;width:","px;top:","position:absolute;font-size:0px;z-index:100;left:0px;width:","task_head_2","task_head_3","SFGanttField","SFGanttField/boolTypes","keydown","selectstart",'TaskNormal',"StatusIcon","HyperlinkAddress,Hyperlink","icon_hyperlink","icon_notes","_Fields_",'dotted 1px #808080',"afterstarttimechange","#DDDDDD","px;height:","scroll_barbg1","scroll_barright1","scroll_barcenter1","scroll_barleft1","scrollend","scrollstart","scroll_barcenter","scroll_barbg","scroll_barright","scroll_barleft","scroll_right","scroll_left","bodyScroll","checkbox","input",'#999999','solid 1px #000000',"contextmenu",'#FF0000',"elementheightchanged","taskchange","taskoutview","taskinview",'#000000','right',"col-resize,se-resize","default","clearSelected","setSelected","getSelected","Selected",'orderdrag.cur,move',"itemHeight",'lineselect.cur,default',"listfieldsresize","listfieldsscroll","list","dblclick","click","#FFFFFF","table","column","arrow_right","arrow_left",'col-resize',"listBody",'center','solid 1px ',"afterscalechange","layoutchange","mapBody","heightspanchange","resize","Month",'icon','symbol','scroll',"afterresize","beforeresize","initialize","left","FieldNames","PreviousUID","afterlinkchange","aftertaskadd","aftertaskmove","aftertaskdelete","aftertaskchange","Assignments","Assignments/Assignment","../Links/*","SuccessorLink","Resources","NextSiblingDataUrl","Tasks","ChildrenDataUrl","CalendarUID","SFDataXmlBase","PredecessorTask","SuccessorTask","PredecessorLink","ExtendedAttribute","FieldID",'ResourceUID','TaskUID','Type','SuccessorUID','PredecessorUID','BaselineFinish','BaselineStart','Critical','LineHeight','Collapse','ClassName','ActualFinish','ActualStart','ConstraintDate','ConstraintType','Notes','PercentComplete','ReadOnly','OutlineLevel','OutlineNumber',"Units","Start","Finish","change","Summary","after","move","NextSibling","FirstChild","getRoot","delete","before","unregister","register","update","linkunregister","linkregister","afterlinkadd","beforelinkadd","Assignment","Link","Resource","Task","url(#default#VML)","behavior:url(#default#VML)","dashed","none","style",_OBS_3('ef_aTS_EGQjTle4kEXpeTPLcTQ8XVqKIYAV'),"solid","transparent","start","mouseup","mousemove","mousedown","Standard","undefined","_SF_E_",'function','100%',"relative","absolute",'hidden',"string","progid:DXImageTransform.Microsoft.Alpha(opacity=","pointer","object"];
	 function SFGlobal(){}
	 function _OBS_6()
	{
		if(!document.all){return false;}
		var reg=new RegExp("MSIE\\s*([0-9]+)");
		var result;
		if(result=reg.exec(navigator.userAgent))
		{
			if(parseInt(result[0])<7){return false}
		}
		return true;
	}
	 function _OBS_7(str)
	{
		var rep,result;
		rep=new RegExp("^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})[ t]([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})(?:\\.[0-9]{1,3})?(?:[\\+\\-][0-9]{1,2}:[0-9]{1,2})?$","ig");
		var result=rep.exec(str);
		if(result)
		{
			return new Date(result[1],result[2]-1,result[3],result[4],result[5],result[6]);
		}
		return new Date(str);
	}
	 function _OBS_8(str,length,initStr)
	{
		if(!str){str=' ';}
		if(!initStr){initStr="";}
		initStr=initStr.toString();
		while(initStr.length<length)
		{
			initStr=str+initStr;
		}
		return initStr;
	}
	 function _OBS_9(time,format,config)
	{
		if(!time){return "";}
		config=config?config:{};
		if(format=='s'){format='yyyy-MM-ddTHH:mm:ss';}
		var year=time.getYear();
		if(year<1900){year+=1900;}

		var arr=[];
		var rep=function (str)
		{
			switch(str)
			{
				case "ddd":
					return config.weekStrs?config.weekStrs[time.getDay()]:time.getDay();
				case "dd":
					return _OBS_8('0',2,time.getDate());
				case "d":
					return time.getDate();
				case "yyyy":
					return _OBS_8('0',4,year);
				case "yy":
					return _OBS_8('0',2,year%100);
				case "MM":
					return _OBS_8('0',2,time.getMonth()+1);
				case "M":
					return time.getMonth()+1;
				case "hhh":
					return Math.ceil((time.getMonth()+1)/6);
				case "HH":
					return _OBS_8('0',2,time.getHours());
				case "H":
					return time.getHours();
				case "mm":
					return _OBS_8('0',2,time.getMinutes());
				case "m":
					return time.getMinutes();
				case "ss":
					return _OBS_8('0',2,time.getSeconds());
				case 's':
					return time.getSeconds();
				case "q":
					return Math.ceil((time.getMonth()+1)/3);
			}
			return str;
		}
		format=format.replace(new RegExp('\\\\([a-zA-Z])','g'),function(a,b){arr.push(b);return '\\';});
		format=format.replace(new RegExp('([a-zA-Z])\\1*','g'),rep);
		format=format.replace(new RegExp('\\\\','g'),function(a){return arr.shift();});
		return format;
	}
	 function _OBS_10(format,params)
	{
		if(typeof(params)!=_OBS_1[184]){params=[params]}
		
		function rep(p1,p2){return params[p2];}
		return format.replace(new RegExp("%([0-9a-zA-Z_]+)","gi"),rep);
	}
	 function _OBS_11(obj,style)
	{
		if(style.indexOf(",")>0)
		{
			var styles=style.split(",");
			for(var i=0;i<styles.length;i++)
			{
				if(_OBS_11(obj,styles[i])){return true;}
			}
			return false;
		}
		try
		{
			if(style.toLowerCase().indexOf(".cur")>0)
			{
				style="url("+style+"),auto";
			}
			style=style.toLowerCase();
			if(style=="hand" && !document.all)
			{
				style=_OBS_1[183];
			}
			obj.style.cursor = style;
			return true;
		}
		catch(e){return false;}
	}
	 function _OBS_12(obj,opacity)
	{
		obj.style.filter=_OBS_1[182]+parseInt(opacity*100)+")";
		obj.style.MozOpacity =opacity;
		obj.style.opacity=opacity;
	}
	 function _OBS_13(array,item,func,all)
	{
		if(!array){return}
		var result=all?[]:null;
		
		func=func?func:function(a,b){return a==b};
		for(var i=array.length-1;i>=0;i--)
		{
			if(func(array[i],item))
			{
				if(all)
				{
					result.push(result);
				}
				else
				{
					return array[i];
				}
			}
		}
		return result;
	}
	 function _OBS_14(array,item,all)
	{
		if(!array){return}
		for(var i=array.length-1;i>=0;i--)
		{
			if(array[i]==item)
			{
				array.splice(i,1)
				if(!all){return array[i];}
			}
		}
	}
	 function _OBS_15(obj,size)
	{
		_OBS_4(obj.style,{width:size[0]+"px",height:size[1]+"px"});
	}
	 function _OBS_16(obj)
	{
		var viewSize=[obj.offsetWidth,obj.offsetHeight]
		if(obj.clientHeight && !document.all){viewSize[1]=obj.clientHeight;}
		if(!viewSize[0])
		{
			viewSize[0]=obj.clientWidth;
		}
		if(!viewSize[0])
		{
			viewSize[0]=parseInt(obj.style.width);
		}
		if(!viewSize[1])
		{
			viewSize[1]=obj.clientHeight;
		}
		if(!viewSize[1])
		{
			viewSize[1]=parseInt(obj.style.height);
		}
		if(!viewSize[0] || !viewSize[1])
		{
			obj=obj.parentElement;
			while(obj)
			{
				if(!viewSize[0] && obj.offsetWidth)
				{
					viewSize[0]=obj.offsetWidth;
				}
				if(!viewSize[1] && obj.offsetHeight)
				{
					viewSize[1]=obj.offsetHeight;
				}
				if(viewSize[0] && viewSize[1])
				{
					break;
				}
				obj=obj.parentElement;
			}
		}
		return viewSize;
	}
	 function _OBS_17(div,rotate)
	{
		rotate=Math.round(rotate%360);
		var rad=rotate*Math.PI/180,style=div.style;
		
		var proName,typeName=_OBS_1[181];
		if(typeof(style[(proName="WebkitTransform")])==typeName || typeof(style[(proName="MozTransform")])==typeName || typeof(style[(proName="transform")])==typeName)
		{
			var transform=(rotate==0)?"":"rotate("+rotate+"deg)",obj={};
			obj[proName]=transform
			_OBS_4(div.style,obj);
			return true;
		}
		
		if(typeof(style.filter)==_OBS_1[181] && document.body.filters)
		{
			
			_OBS_4(div.style,{filter:(rotate==0)?"":"progid:DXImageTransform.Microsoft.Matrix(sizingMethod='auto expand',M11="+Math.cos(rad)+",M12="+(-Math.sin(rad))+",M21="+Math.sin(rad)+",M22="+Math.cos(rad)+")"});
			return true;
		}
		return false;
	}
	 function _OBS_18(obj)
	{
	}
	 function _OBS_19(src,config)
	{
		var img;
		config=config?config:{};
		if(config.spritePoint && (config.spriteSize || config.size))
		{
			if(!config.spriteSize){config.spriteSize=config.size;}
			if(!config.size){config.size=config.spriteSize;}
			img=document.createElement("div");
			_OBS_4(img.style,{overflow:_OBS_1[180],display:'inline-block'});
			if(img.style.position!=_OBS_1[179]){img.style.position=_OBS_1[178];}
			var size=config.imageSize?[config.imageSize[0]*config.size[0]/config.spriteSize[0],config.imageSize[1]*config.size[1]/config.spriteSize[1]]:null;
			var spriteImg=_OBS_19(src,{position:[-config.spritePoint[0]*config.size[0]/config.spriteSize[0],-config.spritePoint[1]*config.size[1]/config.spriteSize[1]],size:size});
			_OBS_4(spriteImg.style,{position:_OBS_1[179]});
			img.appendChild(spriteImg);
		}
		else
		{
			img=document.createElement("img");
			
			
			img.hspace=0;
			_OBS_4(img.style,{border:'0px'});
		}
		if(!config.noLoad){_OBS_20(img,src);}
		if(config.position){_OBS_4(img.style,{left:config.position[0]+"px",top:config.position[1]+"px"});}
		if(config.size){_OBS_4(img.style,{width:config.size[0]+"px",height:config.size[1]+"px"});}
		return img;
	}
	 function _OBS_20(img,src)
	{
		img.src=src;
		var firstChild=img.firstChild;
		while(firstChild)
		{
			if(firstChild.nodeName!="IMG")
			{
				firstChild=firstChild.nextSibling
				continue;
			}
			firstChild.src=src;
			break;
		}
	}
	 function _OBS_21(div,src,config)
	{
		if(!src)
		{
			_OBS_20(div,"");
			_OBS_4(div.style,{backgroundImage:""});
			return;
		}
		config=config?config:{};
		if(config.canPrint)
		{
			var img=_OBS_19(src),sizeDiv=img;
			if(config.spritePoint && config.imageSize)
			{
				sizeDiv=document.createElement("div");
				var isHor=config.repeat=="repeat-x" || config.repeat=="repeat";
				var isVer=config.repeat=="repeat-y" || config.repeat=="repeat";
				_OBS_4(img.style,{position:_OBS_1[178],border:'0px',width:(isHor?_OBS_1[177]:(config.imageSize[0]+"px")),height:(isVer?_OBS_1[177]:(config.imageSize[1]+"px")),left:(isHor?(100*config.spritePoint[0]/config.imageSize[0]+'%'):(-config.spritePoint[0]+"px")),top:(isVer?(100*config.spritePoint[1]/config.imageSize[1]+'%'):(-config.spritePoint[1]+"px")),zIndex:-1});
				sizeDiv.appendChild(img);
			}
			_OBS_4(sizeDiv.style,{position:_OBS_1[179],border:'0px',width:_OBS_1[177],height:_OBS_1[177],left:'0px',top:'0px',zIndex:-1,overflow:_OBS_1[180]});
			div.appendChild(sizeDiv);
		}
		else
		{
			_OBS_4(div.style,{backgroundImage:"url("+src+")"});
			if(config.spritePoint)
			{
				_OBS_4(div.style,{backgroundPosition:(-config.spritePoint[0])+"px "+(-config.spritePoint[1])+"px"});
			}
		}
		if(config.spriteDouble)
		{
			var bar=document.createElement("div");
			_OBS_4(bar.style,{position:_OBS_1[179],fontSize:'0px',left:'0px',top:'0px',width:_OBS_1[177],height:_OBS_1[177],overflow:_OBS_1[180]});
			_OBS_21(bar,src,{canPrint:config.canPrint,spritePoint:(config.repeat=="repeat-x"?{x:config.imageSize[0]/2,y:config.spritePoint[1]}:{x:config.spritePoint[0],y:config.imageSize[1]/2}),imageSize:config.imageSize,repeat:config.repeat});
			div.appendChild(bar);
		}
	}
	_OBS_4(SFGlobal,{setProperty:_OBS_4,isCompatible:_OBS_6,getDate:_OBS_7,getLengthStr:_OBS_8,getDateString:_OBS_9,formatString:_OBS_10,setCursor:_OBS_11,setOpacity:_OBS_12,findInArray:_OBS_13,deleteInArray:_OBS_14,setElementSize:_OBS_15,getElementSize:_OBS_16,setRotate:_OBS_17,setNoPrint:_OBS_18,createImage:_OBS_19,setImageSrc:_OBS_20,setBackgroundImage:_OBS_21});
	 function SFEvent(){}
	 function _OBS_22(obj,handle){return function(){return handle.apply(obj,arguments)};}
	 function _OBS_23(obj){return (obj.tagName || obj.nodeName || obj==window);}
	 function _OBS_24(argu)
	{
		if(!argu){argu=[];}
		if(!argu[0]){argu[0]=window.event;}
		if(argu[0] && !argu[0].target && argu[0].srcElement){argu[0].target=argu[0].srcElement}
		return argu;
	}
	 function _OBS_25(obj,method)
	{
		return function(){method.apply(obj,_OBS_24(arguments));}
	}
	 function _OBS_26(e)
	{
		e=e?e:window.event;
		if(!e){return;}
		if(document.all)
		{
			e.cancelBubble=true;
			e.returnValue=false
		}
		else if(e.stopPropagation)
		{
			e.preventDefault();
			e.stopPropagation();
		}
	}
	 function _OBS_27(e)
	{
		e=e?e:window.event;
		if(!e){return;}
		if(document.all)
		{
			e.cancelBubble=true;
			e.returnValue=true;
		}
		else if(e.stopPropagation)
		{
			e.stopPropagation();
		}
	}
	 function _OBS_28(obj,event,hObj,hMethod,once)
	{
		return _OBS_31(obj,event,_OBS_23(obj)?_OBS_25(hObj,hMethod):_OBS_22(hObj,hMethod),once);
	}
	 function _OBS_29(node,onlyChild)
	{
		if(!node){return;}
		if(node.parentNode && !onlyChild){node.parentNode.removeChild(node);}
		if(!onlyChild)
		{
			_OBS_33(node);
			if(node._SF_E_){node._SF_E_=null;}
		}
		var child;
		while(child=node.firstChild)
		{
			_OBS_29(child);
		}
	}
	 function _OBS_30(handle,listener){return function(){handle.apply(this,arguments);_OBS_32(listener);}}
	 function _OBS_31(obj,event,handle,once)
	{
		var listener=[obj,event];
		if(once){handle=_OBS_30(handle,listener)}
		var type=_OBS_23(obj);
		if(type)
		{
			handle=_OBS_25(obj,handle);
			if(obj.addEventListener){obj.addEventListener(event,handle,false);}
			else if(obj.attachEvent){obj.attachEvent("on"+event,handle);}
			else
			{
				var oldEvent =obj["on"+event];
				if(typeof(oldEvent)==_OBS_1[176])
				{
					obj["on"+event]= function(){oldEvent();handle();};
				}
				else
				{
					obj["on"+event]=handle;
				}
			}
		}
		listener.push(handle);
		if(!obj._SF_E_){obj._SF_E_=[];}
		if(!_OBS_13(obj._SF_E_,event)){obj._SF_E_.push(event);}
		if(obj[_OBS_1[175]+event] && type!="shape")
		{
			obj[_OBS_1[175]+event].push(listener);
		}
		else
		{
			obj[_OBS_1[175]+event]=(type=="shape")?[]:[listener];
		}
		if(!SFEvent.allEvents){SFEvent.allEvents=[];}
		if(event!="unload"){SFEvent.allEvents.push(listener);}
		return listener;
	}
	 function _OBS_32(listener)
	{
		if(!listener || listener.length==0){return;}
		try{
		if(_OBS_23(listener[0]))
		{
			if(listener[0].removeEventListener)
			{
				listener[0].removeEventListener(listener[1],listener[2],false);
			}
			else if(listener[0].detachEvent)
			{
				listener[0].detachEvent("on"+listener[1],listener[2]);
			}
			else
			{
				listener[0]["on"+listener[1]]=null;
			}
		}
		}catch(e){}
		_OBS_14(listener[0][_OBS_1[175]+listener[1]],listener);
		_OBS_14(SFEvent.allEvents,listener);
		while(listener.length>0){listener.pop()}
	}
	 function _OBS_33(obj,event)
	{
		if(!obj || !obj._SF_E_){return;}
		if(!event)
		{
			for(var i=obj._SF_E_.length-1;i>=0;i--)
			{
				_OBS_33(obj,obj._SF_E_[i]);
			}
			return;
		}
		var listener,listeners=obj[_OBS_1[175]+event];
		while(listener=listeners.pop()){_OBS_32(listener)}
	}
	 function _OBS_34()
	{
		var listeners=SFEvent.allEvents;
		if(listeners)
		{
			for(var i=listeners.length-1;i>=0;i--)
			{
				_OBS_32(listeners[i]);
			}
		}
		SFEvent.allEvents=null;
	}
	 function _OBS_35(obj,event,args)
	{
		if(_OBS_23(obj))
		{
			try{
			if(obj.fireEvent){obj.fireEvent("on"+event);}
			if(obj.dispatchEvent){obj.dispatchEvent(event);}
			}catch(e){}
		}
		if(!args){args=[];}
		var listeners=obj[_OBS_1[175]+event];
		if(listeners&&listeners.length>0)
		{
			for(var i=0;i<listeners.length;i++)
			{
				var listener=listeners[i];
				if(listener && listener[2])
				{
					listener[2].apply(obj,args);
				}
			}
		}
	}
	 function _OBS_36(obj,container)
	{
		var point=[0,0];
		var a=obj;
		while(a && a.offsetParent && a!=container)
		{
			point[0]+=a.offsetLeft;
			point[1]+=a.offsetTop;
			a=a.offsetParent
			if(a)
			{
				point[0]-=a.scrollLeft;
				point[1]-=a.scrollTop;
			}
		}
		return point;
	}
	 function _OBS_37(e,container)
	{
		if(typeof e.clientX!=_OBS_1[174])
		{
			var rect = container.getBoundingClientRect(); 
			return [e.clientX-rect.left, e.clientY-rect.top] 
		}
		
		if(typeof e.pageX!=_OBS_1[174])
		{
			var offset=_OBS_36(container);
			return [e.pageX-offset[0],e.pageY-offset[1]];
		}
		return [0,0];
	}
	 function _OBS_38(e,container)
	{
		return _OBS_37(e,container);
	}
	 function _OBS_39(e)
	{
		return document.all?e.button:(e.button==2?2:1);
	}
	 function _OBS_40(obj)
	{
		if(document.all)
		{
			obj.unselectable="on";
			obj.onselectstart=_OBS_41
		}
		else
		{
			obj.style.MozUserSelect="text";
		}
	}
	 function _OBS_41()
	{
		return false;
	}
	 function _OBS_42()
	{
		if(!SFEvent._ganttUnloadListener){SFEvent._ganttUnloadListener=_OBS_31(window,"unload",_OBS_34);}
	}
	_OBS_4(SFEvent,{getCallback:_OBS_22,isHtmlControl:_OBS_23,getEvent:_OBS_24,createAdapter:_OBS_25,cancelBubble:_OBS_26,returnTrue:_OBS_27,bind:_OBS_28,deposeNode:_OBS_29,runOnceHandle:_OBS_30,addListener:_OBS_31,removeListener:_OBS_32,clearListeners:_OBS_33,clearAllListeners:_OBS_34,trigger:_OBS_35,getPageOffset:_OBS_36,getEventPosition:_OBS_37,getEventRelative:_OBS_38,getEventButton:_OBS_39,setUnSelectable:_OBS_40,falseFunction:_OBS_41,load:_OBS_42});
	 function SFAjax(){}
	 function _OBS_43()
	{
		if(window.XMLHttpRequest)
		{
			return new window.XMLHttpRequest();
		}
		else if(typeof(ActiveXObject)!=_OBS_1[174])
		{
			return new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	 function _OBS_44(path,handle,async,data)
	{
		var doc;
		if(location.protocol.indexOf(_OBS_3("ef_aTP"))!=0 && path.indexOf(_OBS_3("ef_aTS_EGJ"))!=0)
		{
		
			try
			{
				doc=_OBS_45();
				doc.load(path);
			}
			catch(e){}
			if(doc && doc.documentElement)
			{
				if(handle)
				{
					handle.apply(null,[doc]);
				}
				doc=null;
				return;
			}
		}
		var request=_OBS_43(),triggered=false;
		var onReadyStateChange=_OBS_22(this,function()
		{
			triggered=true;
			if(request.readyState==4)
			{
				var doc=request.responseXML;
				if(!doc.documentElement)
				{
					doc=_OBS_45(request.responseText);
				}
				if(!doc ||!doc.documentElement)
				{
					handle();
					return;
				}
				if(handle)
				{
					handle.apply(null,[doc]);
				}
				doc=null;
				_OBS_33(request);
				request=null;
			}
		});
		request.onreadystatechange=onReadyStateChange;
		request.open(data?"POST":"GET",path,!!async);
		request.send(data?data:null);
		if(!async && !triggered)
		{
			onReadyStateChange();
		}
	}
	 function _OBS_45(xmlStr)
	{
		var doc;
		if(typeof(ActiveXObject)!=_OBS_1[174]&&typeof(window.GetObject)!=_OBS_1[174])
		{
			try{doc=new ActiveXObject("Msxml2.DOMDocument");}
			catch(e){doc=new ActiveXObject("Msxml.DOMDocument");}
			if(xmlStr)
			{
				doc.loadXML(xmlStr);	
			}
		}
		else
		{
			if(xmlStr)
			{
				if(typeof DOMParser!=_OBS_1[174])	
				{
					doc=new DOMParser().parseFromString(xmlStr,"text/xml")
				}
			}
			else
			{
				if(document.implementation && document.implementation.createDocument)
				{
					doc=document.implementation.createDocument("","",null);
				}
			}
		}
		return doc;
	}
	 function _OBS_46(node,xpath)
	{
		var paths=xpath.split("/");
		for(var i=0;i<paths.length;i++)
		{
			if(!node){return node;}
			var path=paths[i];
			if(path=="..")
			{
				node=node.parentNode;
				continue;
			}
			var child;
			for(child=node.firstChild;child;child=child.nextSibling){if(path=="*" || path==child.nodeName){break;}}
			node=child;
			continue;
		}
		return node;
	}
	 function _OBS_47(node)
	{
		if(!node || typeof(node)!=_OBS_1[184]){return node;}
		return node.text?node.text:(node.childNodes[0]?node.childNodes[0].nodeValue:"");
	}
	 function _OBS_48(node,value)
	{
		while(node.firstChild){node.removeChild(node.firstChild)}
		node.appendChild(node.ownerDocument.createTextNode(value));
	}
	 function _OBS_49(doc)
	{
		return doc.xml?doc.xml:new window.XMLSerializer().serializeToString(doc);
	}
	 function _OBS_50(child,nodeName)
	{
		while(child)
		{
			if(!nodeName || nodeName==child.nodeName)
			{
				return child;
			}
			child=child.nextSibling;
		}
		return null;
	}
	 function _OBS_51(str,password)
	{
		password=password==false?password:_OBS_Password;
		var passIndex,passLength;
		if(password)
		{
			passIndex=0;
			passLength=password.length;
		}
		var num=0,byt=0;
		var len=str.length;
		var resultStr="";
		for(var i=0;i<len;i++)
		{
			var code=str.charCodeAt(i);
			if(code>=2048)		
			{
				byt=(byt<<24)+(((code>>12)|0xe0)<<16)+((((code&0xfff)>>6)|0x80)<<8)+((code&0x3f)|0x80);
				num+=24;
			}
			else if(code>=128)	
			{
				byt=(byt<<16)+(((code>>6)|0xc0)<<8)+((code&0x3f)|0x80);
				num+=16;
			}
			else			
			{
				num+=8;
				byt=(byt<<8)+code;
			}
			while(num>=6)
			{
				var b=byt>>(num-6);
				byt=byt-(b<<(num-6));
				num-=6;
				if(password)
				{
					b=(b+password.charCodeAt(passIndex++))%64;
					passIndex=passIndex%passLength;
				}
				
				var code=(b<=9)?(b+48):((b<=35)?(b+55):((b<=61)?(b+61):((b==62)?44:95)));
				resultStr+=String.fromCharCode(code);
			}
		}
		if(num>0)
		{
			var b=byt<<(6-num);
			if(password)
			{
				b=(b+password.charCodeAt(passIndex++))%64;
				passIndex=passIndex%passLength;
			}

			resultStr+=String.fromCharCode((b<=9)?(b+48):((b<=35)?(b+55):((b<=61)?(b+61):((b==62)?44:95))));
		}
		return resultStr;
	}
	_OBS_4(SFAjax,{createHttpRequest:_OBS_43,loadXml:_OBS_44,createDocument:_OBS_45,selectSingleNode:_OBS_46,getNodeValue:_OBS_47,setNodeValue:_OBS_48,getXmlString:_OBS_49,getNextSibling:_OBS_50,encode:_OBS_51,decode:_OBS_3});
	 function SFConfig(obj)
	{
		this.obj=obj?obj:{};
		this.inited=false;
		if(!obj){_OBS_52(this.obj,window._SFGantt_config,false);}
	}
	_OBS_4(SFConfig.prototype,{getConfig: function(path,dv)
	{
		if(!this.inited)
		{
			this.inited=true;
			this.parseWildcard();
		}
		var obj=this.getConfigObj(path);
		return typeof(obj)!=_OBS_1[174]?obj:dv
	},getConfigObj: function(path)
	{
		if(!this.inited)
		{
			this.inited=true;
			this.parseWildcard();
		}
		var paths=path.split(new RegExp("[/\\.]"));
		var name,obj=this.obj;
		while(typeof(name=paths.shift())==_OBS_1[181])
		{
			if(!name){continue;}
			if(!obj || typeof(obj)!=_OBS_1[184]){break;}
			obj=obj[name];
		}
		return obj;
	},setConfig: function(path,value,cover)
	{
		var paths=path.split(new RegExp("[/\\.]"));
		var name,obj=this.obj;
		while(name=paths.shift())
		{
			if(paths[0])
			{
				if(!obj[name] || typeof(obj[name])!=_OBS_1[184])
				{
					obj[name]={};
				}
				obj=obj[name];
			}
			else
			{
				if(cover!=false || !obj[name])
				{
					obj[name]=value;
				}
			}
		}
	},parseWildcard: function(obj)
	{
		if(!obj){obj=this.obj;}
		if(!obj){return;}
		for (var key in obj)
		{
			switch (typeof(obj[key]))
			{
				case _OBS_1[184]:
					this.parseWildcard(obj[key]);
					break;
				case _OBS_1[181]:
					if(obj[key].indexOf("${")>=0)
					{
						var config=this;
						obj[key]=obj[key].replace(new RegExp("\\$\\{([^\\}]+)\\}\\$",'g'),function(a,b){return config.getConfig(b)});
					}
					break;
			}
		}
	}});
	 function _OBS_52(obj,json,cover)
	{
		if(!json){return;}
		for (var key in json)
		{
			switch (typeof(json[key]))
			{
				case _OBS_1[176]:
					break;
				case _OBS_1[184]:
					if(_OBS_23(json[key]))
					{
						obj[key]=json[key];
						continue;
					}
					if(!obj[key]){obj[key]={};}
					_OBS_52(obj[key],json[key],cover);
					break;
				default:
					if(cover!=false || !obj[key])
					{
						obj[key]=json[key];
					}
					break;
			}
		}
	}
	 function _OBS_53(obj,json)
	{
		if(!json){return;}
		for(var key in json)
		{
			if(typeof(json[key])==_OBS_1[176]){continue;}
			obj[key]=json[key];
		}
	}
	_OBS_4(SFConfig,{addConfig:_OBS_52,applyProperty:_OBS_53});
	 function SFImgLoader(src)
	{
		this.imgs=[];
		var img=new Image();
		this.img=img;
		_OBS_28(img,"load",this,this.onLoad);
		img.src=src;
		if(img.complete){this.onLoad();}
	}
	_OBS_4(SFImgLoader.prototype,{addImg: function(img)
	{
		this.imgs.push(img);
		if(this.loaded)
		{
			this.onLoad();
		}
	},onLoad: function()
	{
		this.loaded=true;
		var img;
		while(img=this.imgs.pop())
		{
			if(img.tagName.toLowerCase()=="img")
			{
				img.src=this.img.src;
			}
			else
			{
				img.style.backgroundImage="url("+this.img.src+")";
			}
		}
	},depose: function()
	{
		this.imgs.length=0;
		_OBS_33(this);
		for(var key in this){this[key]=null;}
	}});
	 function _OBS_54(img,src)
	{
		if(!SFImgLoader.objs){SFImgLoader.objs={};}
		if(!SFImgLoader.objs[src]){SFImgLoader.objs[src]=new SFImgLoader(src);}
		SFImgLoader.objs[src].addImg(img);
	}
	 function _OBS_55(img,src)
	{
		if(SFImgLoader.objs)
		{
			for(var key in SFImgLoader.objs)
			{
				if(!SFImgLoader.objs[key] instanceof SFImgLoader){continue;}
				var loader=SFImgLoader.objs[key];
				if(loader){loader.depose();}
				SFImgLoader.objs[key]=null;
				delete SFImgLoader.objs[key];
			}
		}
	}
	_OBS_4(SFImgLoader,{setImageSrc:_OBS_54,depose:_OBS_55});
	 function SFWorkingCalendar(func)
	{
		this.getWorkTime=func;
	}
	 function _OBS_56(name)
	{
		switch(name)
		{
			case "AnyDay":
				return new SFWorkingCalendar(_OBS_57(
					[
						[[480,720],[780,1020]],
						[[480,720],[780,1020]],
						[[480,720],[780,1020]],
						[[480,720],[780,1020]],
						[[480,720],[780,1020]],
						[[480,720],[780,1020]],
						[[480,720],[780,1020]]
					],
						[]
					));
			case _OBS_1[173]:
				return new SFWorkingCalendar(_OBS_57(
					[
						[],
						[[480,720],[780,1020]],
						[[480,720],[780,1020]],
						[[480,720],[780,1020]],
						[[480,720],[780,1020]],
						[[480,720],[780,1020]],
						[]
					],
					[
				
				
					]
					))
			case "AnyTime":
			default:
				return new SFWorkingCalendar(function(){return [Number.MIN_VALUE,Number.MIN_VALUE];});
		}
	}
	 function _OBS_57(wds,exceptions)
	{
		return function(time)
		{
			return _OBS_58(time,wds,exceptions);
		}
	}
	 function _OBS_58(time,wds,exceptions)
	{
		var stv,ds=(time.valueOf()-time.getTimezoneOffset()*60*1000)%(24*60*60*1000);
		var t=time.valueOf()-ds;
		for(var i=0;i<exceptions.length;i++)
		{
			var exception=exceptions[i];
			if(exceptions[i][0].valueOf()<=t && exceptions[i][1].valueOf()>time.valueOf())
			{
				if(exception[2].length==0)
				{
					return _OBS_58(exceptions[i][1],wds,exceptions);
				}
				
				for(var i=0;i<exception[2].length;i++)
				{
					var wd=exception[2][i];
					if(ds<wd[1]*60*1000)
					{
						return [new Date(t+wd[0]*60*1000),new Date(t+wd[1]*60*1000)];
					}
				}
				
				return _OBS_58(new Date(t+24*60*60*1000),wds,exceptions);
			}
		}
		var day=time.getDay();
		for(var i=0;i<wds[day].length;i++)
		{
			var wd=wds[day][i];
			if(ds<wd[1]*60*1000)
			{
				return [new Date(t+wd[0]*60*1000),new Date(t+wd[1]*60*1000)];
			}
		}
		return _OBS_58(new Date(t+24*60*60*1000),wds,exceptions);
	}
	_OBS_4(SFWorkingCalendar,{getCalendar:_OBS_56,WT_WeekDay:_OBS_57,WT_WeekDayCal:_OBS_58});
	 function SFDragObject(div,handle,config)
	{
		_OBS_4(this,{div:div,handle:handle,container:div,interval:256});
		_OBS_4(this,config);
	}
	_OBS_4(SFDragObject.prototype,{onMouseDown: function(e)
	{
		_OBS_26(e);
		var div=this.div,doc=div.ownerDocument;
		if(div.setCapture){div.setCapture();}
		var point=_OBS_37(e,this.container);
		_OBS_4(this,{
			ml:_OBS_28(doc,_OBS_1[171],this,this.onMouseMove),
			ul:_OBS_28(doc,_OBS_1[170],this,this.onMouseUp),
			sp:point,
			lp:point,
			timeout:window.setInterval(_OBS_22(this,this.onTime),this.interval)
		});
		
		if(this.rtp)
		{
			var style=div.style;
			this.dsp={x:parseInt(style.left),y:parseInt(style.top)}
		}
		this.handle(point,point,_OBS_1[169]);
	},onMouseMove: function(e)
	{
		_OBS_26(e);
		var point=_OBS_37(e,this.container),rtp=this.rtp;
		this.lp=point;
		this.moveed=true;
		if(rtp)
		{
			var dsp=this.dsp,sp=this.sp;
			var px=dsp.x+rtp.x*(point.x-sp.x),py=dsp.y+rtp.y*(point.y-sp.y);
			var rtpLimit=this.rtpLimit;
			if(rtpLimit)
			{
				if(rtpLimit.minX){px=Math.max(px,rtpLimit.minX);}
				if(rtpLimit.maxX){px=Math.min(px,rtpLimit.maxX);}
				if(rtpLimit.minY){py=Math.max(py,rtpLimit.minY);}
				if(rtpLimit.maxY){py=Math.min(py,rtpLimit.maxY);}
			}
			_OBS_4(this.div.style,{left:px+"px",top:py+"px"});
		}
	},onTime: function()
	{
		if(this.div && this.moveed)
		{
			this.handle(this.sp,this.lp);
			this.moveed=false;
		}
	},onMouseUp: function(e)
	{
		this.onTime();
		var doc=this.div.ownerDocument;
		_OBS_26(e);
		_OBS_32(this.ml);
		_OBS_32(this.ul);
		window.clearInterval(this.timeout);
		delete this.div;
		delete this.container;
		if(doc.releaseCapture){doc.releaseCapture();}
		this.handle(this.sp,this.lp,"end");
	}});
	 function _OBS_59(div,handle,config)
	{
		return _OBS_31(div,_OBS_1[172],function(e)
		{
			if(_OBS_39(e)!=1){return;}
			var obj=new SFDragObject(div,handle,config);
			obj.onMouseDown(e);
		});
	}
	_OBS_4(SFDragObject,{setup:_OBS_59});
	 function SFGraphics()
	{
		if(arguments.length<1){return;}
		this.div=document.createElement("div");
	}
	_OBS_4(SFGraphics.prototype,{getPanel: function(){return this.div;},start: function(){},moveTo: function(){},lineTo: function(){},finish: function(){},clear: function(){},setScale: function(){},setPosition: function(position)
	{
		_OBS_4(this.div.style,{position:_OBS_1[179],left:position.x+"px",top:position.y+"px"});
	},setLineColor: function(){},setFillColor: function(){},setOpacity: function(){},setLineWeight: function(){},setLineStyle: function(){}});
	 function SFGraphicsCanvas()
	{
		var div=this.div=document.createElement("canvas");
		_OBS_4(div.style,{position:_OBS_1[179],zIndex:420});
	}
	SFGraphicsCanvas.prototype=new SFGraphics()
	_OBS_4(SFGraphicsCanvas.prototype,{start: function(origin,scale,size)
	{
		_OBS_4(this,{origin:origin,size:size,scale:scale,pathArr:[]});
	},moveTo: function(point)
	{
		var pathArr=this.pathArr,scale=this.scale,origin=this.origin;
		pathArr.push({type:"m",argu:[(point.x-origin.x)/scale,(point.y-origin.y)/scale]});
	},lineTo: function(point)
	{
		var pathArr=this.pathArr,scale=this.scale,origin=this.origin;
		pathArr.push({type:'l',argu:[(point.x-origin.x)/scale,(point.y-origin.y)/scale]});
	},finish: function()
	{
		this.lastScale=this.scale;
		this.reDraw();
	},clear: function()
	{
		var ctx=this.div.getContext("2d");
	},setScale: function(scale)
	{
		this.lastScale=scale;
		var size=this.size;
		_OBS_4(this.div.style,{width:size.x/scale+"px",height:size.y/scale+"px"});
		this.reDraw();
	},reDraw: function()
	{
		var scale=this.scale,lastScale=this.lastScale,size=this.size,div=this.div,pathArr=this.pathArr;
		if(!size || !pathArr || pathArr.length==0){return;}
		_OBS_4(div,{width:size.x/lastScale,height:size.y/lastScale});
		var ctx=div.getContext("2d");
		_OBS_4(ctx,{
			lineCap:"round",
			lineJoin:"round",
			fillStyle:this.bgcolor,
			lineWidth:this.lineWeight/scale*lastScale,
			strokeStyle:this.lineColor,
			globalAlpha:this.opacity
		});
		ctx.beginPath();
		ctx.scale(scale/lastScale,scale/lastScale);
		for(var i=0;i<pathArr.length;i++)
		{
			var p=pathArr[i];
			switch(p.type)
			{
				case "m":
					ctx.moveTo.apply(ctx,p.argu);
					break;
				case 'l':
					ctx.lineTo.apply(ctx,p.argu);
					break;
			}
		}
		if(this.bgcolor){ctx.fill();}
		if(this.lineColor){ctx.stroke();}
	},setLineColor: function(color)
	{
		if(color==_OBS_1[168]){color=""}
		this.lineColor=color;
		this.reDraw();
	},setFillColor: function(color)
	{
		if(color==_OBS_1[168]){color=""}
		this.bgcolor=color;
		this.reDraw();
	},setOpacity: function(opacity)
	{
		this.opacity=opacity;
		this.reDraw();
	},setLineWeight: function(weight)
	{
		this.lineWeight=weight;
		this.reDraw();
	}});
	 function _OBS_60()
	{
		if(typeof(SFGraphicsCanvas._isSupport)==_OBS_1[174])
		{
			SFGraphicsCanvas._isSupport=!!document.createElement("canvas").getContext
		}
		return SFGraphicsCanvas._isSupport;
	}
	_OBS_4(SFGraphicsCanvas,{isSupport:_OBS_60});
	 function SFGraphicsDiv()
	{
		var div=this.div=document.createElement("div");
		_OBS_4(div.style,{position:_OBS_1[179],zIndex:420});
		this.ctx=new window.jsGraphics(div);
	}
	SFGraphicsDiv.prototype=new SFGraphics()
	_OBS_4(SFGraphicsDiv.prototype,{start: function(origin,scale,size)
	{
		_OBS_4(this,{origin:origin,size:size,scale:scale,pathArr:[]});
	},moveTo: function(point)
	{
		var pathArr=this.pathArr,scale=this.scale,origin=this.origin;
		var arr={xPoints:[(point.x-origin.x)/scale],yPoints:[(point.y-origin.y)/scale]};
		pathArr.push(arr);
	},lineTo: function(point)
	{
		var pathArr=this.pathArr,arr=pathArr[pathArr.length-1],scale=this.scale,origin=this.origin;
		arr.xPoints.push((point.x-origin.x)/scale);
		arr.yPoints.push((point.y-origin.y)/scale);
	},finish: function()
	{
		this.lastScale=this.scale;
		this.reDraw();
	},setScale: function(scale)
	{
		this.lastScale=scale;
		var size=this.size;
		_OBS_4(this.div.style,{width:size.x/scale+"px",height:size.y/scale+"px"});
		this.reDraw();
	},setLineColor: function(color)
	{
		if(color==_OBS_1[168]){color=""}
		this.lineColor=color;
		this.reDraw();
	},setFillColor: function(color)
	{
		if(color==_OBS_1[168]){color=""}
		this.bgcolor=color;
		this.reDraw();
	},setOpacity: function(opacity)
	{
		_OBS_4(this.div.style,{
			filter:_OBS_1[182]+parseInt(opacity*100)+")",
			MozOpacity:opacity,
			opacity:opacity
		});
	},setLineWeight: function(weight)
	{
		this.lineWeight=weight;
		this.reDraw();
	},setLineStyle: function(style)
	{
		this.lineStyle=style.toLowerCase();
	},reDraw: function()
	{
		var scale=this.scale,lastScale=this.lastScale,size=this.size,div=this.div,pathArr=this.pathArr;
		if(!size || !pathArr || pathArr.length==0){return;}
		_OBS_4(div,{width:size.x/lastScale,height:size.y/lastScale});
		var ctx=this.ctx;
		ctx.clear();
		for(var i=0;i<pathArr.length;i++)
		{
			var p=pathArr[i],xPoints,yPoints;
			if(scale==lastScale)
			{
				xPoints=p.xPoints;
				yPoints=p.yPoints;
			}
			else
			{
				xPoints=new Array(p.xPoints.length);
				yPoints=new Array(p.yPoints.length);
				var s=scale/lastScale,pxs=p.xPoints,pys=p.yPoints;
				for(var j=xPoints.length-1;j>=0;j--)
				{
					xPoints[j]=pxs[j]*s
					yPoints[j]=pys[j]*s
				}
			}
			if(this.bgcolor)
			{
				ctx.setColor(this.bgcolor);
				ctx.fillPolygon(xPoints,yPoints);
			}
			if(this.lineColor)
			{
				ctx.setColor(this.lineColor);
				ctx.setStroke((this.lineStyle && this.lineStyle!=_OBS_1[167] && window.Stroke)?window.Stroke.DOTTED:this.lineWeight);
				ctx.drawPolyline(xPoints,yPoints);
			}
		}
		ctx.paint();
	}});
	 function _OBS_61()
	{
		return !!window.jsGraphics;
	}
	_OBS_4(SFGraphicsDiv,{isSupport:_OBS_61});
	 function SFGraphicsSvg()
	{
		var svgNs=_OBS_1[166];
		var div=this.div=document.createElementNS(svgNs,'svg');
		_OBS_4(div.style,{position:_OBS_1[179],zIndex:420});
		var path=this.path=document.createElementNS(svgNs,'path');
		div.appendChild(path);
	}
	SFGraphicsSvg.prototype=new SFGraphics()
	_OBS_4(SFGraphicsSvg.prototype,{start: function(origin,scale,size)
	{
		_OBS_4(this,{origin:origin,size:size,scale:scale,pathArr:[]});
	},moveTo: function(point)
	{
		var pathArr=this.pathArr,scale=this.scale,origin=this.origin;
		pathArr.push("M");
		pathArr.push((point.x-origin.x)/scale);
		pathArr.push((point.y-origin.y)/scale);
	},lineTo: function(point)
	{
		var pathArr=this.pathArr,scale=this.scale,origin=this.origin;
		pathArr.push("L");
		pathArr.push((point.x-origin.x)/scale);
		pathArr.push((point.y-origin.y)/scale);
	},finish: function()
	{
		var pathArr=this.pathArr;
		this.path.setAttribute("d",this.pathArr.join(' '));
		this.setScale(this.scale);
	},clear: function(){this.path.setAttribute("d","");},setScale: function(scale)
	{
		var size=this.size,lineWeight=this.lineWeight;
		if(!size){return;}
		_OBS_4(this.div.style,{width:size.x/scale+lineWeight*2+"px",height:size.y/scale+lineWeight*2+"px"});
		this.path.setAttribute("transform","scale("+(this.scale/scale)+") translate("+(lineWeight)+","+(lineWeight)+")");
		this.lastScale=scale;
		this.path.setAttribute(_OBS_1[165],this.getStyle());
	},setPosition: function(position)
	{
		if(!position){return;}
		this.lastPosition=position;
		var lineWeight=this.lineWeight;
		_OBS_4(this.div.style,{position:_OBS_1[179],left:(position.x-lineWeight)+"px",top:(position.y-lineWeight)+"px"});
	},setLineColor: function(color)
	{
		if(color==_OBS_1[168] || !color){color=_OBS_1[164]}
		this.lineColor=color;
		this.path.setAttribute(_OBS_1[165],this.getStyle());
	},setFillColor: function(color)
	{
		if(color==_OBS_1[168] || !color){color=_OBS_1[164]}
		this.bgcolor=color;
		this.path.setAttribute(_OBS_1[165],this.getStyle());
	},setOpacity: function(opacity)
	{
		this.opacity=opacity;
		this.path.setAttribute(_OBS_1[165],this.getStyle());
	},setLineWeight: function(weight)
	{
		this.lineWeight=weight;
		
		this.setScale(this.lastScale);
		this.setPosition(this.lastPosition);
		
	},setLineStyle: function(style)
	{
		var dashArray;
		switch(style.toLowerCase())
		{
			case "dotted":
				dashArray=[1,6];
				break;
			case _OBS_1[163]:
				dashArray=[6,6];
				break;
			default:
				break;
		}
		this.dashArray=dashArray;
		this.path.setAttribute(_OBS_1[165],this.getStyle());
	},getStyle: function()
	{
		var arr=[];
		arr.push("fill:none");
		arr.push("opacity:"+this.opacity);
		arr.push("stroke:"+this.lineColor);
		arr.push("stroke-linecap:round");
		arr.push("stroke-linejoin:round");
		arr.push("stroke-dasharray:"+this.dashArray);
		arr.push("stroke-width:"+this.lineWeight/this.scale*this.lastScale);
		arr.push("fill:"+this.bgcolor);
		return arr.join(";");
	}});
	 function _OBS_62()
	{
		if(typeof(SFGraphicsSvg._isSupport)==_OBS_1[174])
		{
			if(document.createElementNS)
			{
				var svgNs=_OBS_1[166];
				var div=document.createElementNS(svgNs,'svg');
				SFGraphicsSvg._isSupport=typeof(div.ownerSVGElement)==_OBS_1[184];
			}
			else
			{
				SFGraphicsSvg._isSupport=false;
			}
		}
		return SFGraphicsSvg._isSupport;
	}
	_OBS_4(SFGraphicsSvg,{isSupport:_OBS_62});
	 function SFGraphicsVml()
	{
		
		
		
		var div=this.div=document.createElement("v:shape");
		_OBS_4(div,{unselectable:"on",filled:'true'});
		var stroke=this.stroke=document.createElement("v:stroke");
		_OBS_4(stroke,{joinstyle:"round",endcap:"round"});
		div.appendChild(stroke);
		var fill=this.fill=document.createElement("v:fill");
		div.appendChild(fill);
		stroke.style.cssText=fill.style.cssText=div.style.cssText=_OBS_1[162];
		stroke.style.behavior=fill.style.behavior=div.style.behavior=_OBS_1[161];
		div.style.position=_OBS_1[179];
		div.style.zIndex='420';
	}
	SFGraphicsVml.prototype=new SFGraphics()
	_OBS_4(SFGraphicsVml.prototype,{setPosition: function(position)
	{
		var div=this.div;
		div.style.position=_OBS_1[179]
		div.style.left=position.x+"px"
		div.style.top=position.y+"px"
	},start: function(origin,scale,size)
	{
		_OBS_4(this,{origin:origin,size:size,scale:scale,pathArr:[]});
		this.div.coordsize=parseInt(size.x*256/scale)+","+parseInt(size.y*256/scale)
	},moveTo: function(point)
	{
		var pathArr=this.pathArr,scale=this.scale,origin=this.origin;
		pathArr.push("m");
		pathArr.push(parseInt((point.x-origin.x)*256/scale));
		pathArr.push(parseInt((point.y-origin.y)*256/scale));
	},lineTo: function(point)
	{
		var pathArr=this.pathArr,scale=this.scale,origin=this.origin;
		pathArr.push('l');
		pathArr.push(parseInt((point.x-origin.x)*256/scale));
		pathArr.push(parseInt((point.y-origin.y)*256/scale));
	},finish: function()
	{
		var pathArr=this.pathArr;
		pathArr.push("e");
		this.div.path=this.pathArr.join(' ');
		this.setScale(this.scale);
	},clear: function()
	{
		this.div.style.display=_OBS_1[164]
		this.div.path="e";
		this.div.style.display=""
	},setScale: function(scale)
	{
		var div=this.div,size=this.size;
		div.style.width=size.x/scale+"px"
		div.style.height=size.y/scale+"px"
	},setLineColor: function(color)
	{
		var div=this.div;
		if(color==_OBS_1[168] || color=="")
		{
			div.stroked=false;
		}
		else
		{
			div.stroked=true;
			div.strokecolor=color;
		}
	},setFillColor: function(color)
	{
		var div=this.div;
		if(color==_OBS_1[168] || color=="")
		{
			div.filled=false;
		}
		else
		{
			div.filled=true;
			div.fillcolor=color;
		}
	},setOpacity: function(opacity)
	{
		this.stroke.opacity=opacity;
		this.fill.opacity=opacity;
	},setLineWeight: function(weight){this.div.strokeweight=weight;},setLineStyle: function(style)
	{
		switch(style.toLowerCase())
		{
			case "dotted":
				style="ShortDot";
				break;
			case _OBS_1[163]:
				style="ShortDash";
				break;
		}
		
		
		this.stroke.dashstyle=style;
	}});
	 function _OBS_63()
	{
		
		if(typeof(SFGraphicsVml._isSupport)==_OBS_1[174])
		{
			
			
			
			try{
				
				
				if(document.namespaces)
				{
					if(!document.namespaces.v){document.namespaces.add("v","urn:schemas-microsoft-com:vml");}
					var div=document.createElement("v:shape");
					div.style.cssText=_OBS_1[162]
					div.style.behavior=_OBS_1[161]
					document.body.appendChild(div);
					
					SFGraphicsVml._isSupport=typeof(div.Path)==_OBS_1[184];
					div.parentNode.removeChild(div);
				}
				else
				{
					SFGraphicsVml._isSupport=false;
				}
			}
			catch(e){SFGraphicsVml._isSupport=false;}
		}
		return SFGraphicsVml._isSupport;
	}
	_OBS_4(SFGraphicsVml,{isSupport:_OBS_63});
	 function SFData(adapter,config)
	{
		config=config?config:new SFConfig();
		_OBS_53(this,config.getConfigObj("SFData"));
		_OBS_4(this,{modules:[],adapter:adapter,components:[],rootElement:{},lastElement:{},elementUids:{}});
		adapter.initialize();
		this.initialize();
	}
	_OBS_4(SFData.prototype,{initialize: function()
	{
		this.addTreeModule(_OBS_1[160]);
		this.addTreeModule(_OBS_1[159]);
		this.addModule(_OBS_1[158]);
		this.addModule(_OBS_1[157]);

		
		this.addLink=function(sucTask,preTask,type)
		{
			
			
			if(!this.checkEvent(_OBS_1[156],[sucTask,preTask,type])){return false;}
			var link=this.adapter.addLink(sucTask,preTask,type);
			link.PredecessorTask=preTask;
			link.SuccessorTask=sucTask;
			link.Type=type;
			this.registerLink(link);
			
			_OBS_35(this,_OBS_1[155],[link]);
			return link;
		}
		
		this.addAssignment=function(task,resource,unit)
		{
			
			
			if(!this.checkEvent("beforeassignmentadd",[task,resource,unit])){return false;}
			var assignment=this.adapter.addAssignment(task,resource,unit);
			assignment.task=task;
			assignment.resource=resource;
			this.registerAssignment(assignment);
			
			_OBS_35(this,"afterassignmentadd",[assignment]);
			return assignment;
		}
		
		this._registerLink=function(link)
		{
			if(!link.SuccessorTask){link.SuccessorTask=this.getTaskByUid(link.SuccessorUID,false);}
			if(!link.PredecessorTask){link.PredecessorTask=this.getTaskByUid(link.PredecessorUID,false);}
			if(link.SuccessorTask&&link.PredecessorTask)
			{
				this.registerLink(link);
				return;
			}
			if(!this.afterTaskLinks){this.afterTaskLinks={};}
			if(link.SuccessorUID && !link.SuccessorTask)
			{
				var uid=link.SuccessorUID;
				if(!this.afterTaskLinks[uid])this.afterTaskLinks[uid]=[];
				this.afterTaskLinks[uid].push(link);
			}
			if(link.PredecessorUID && !link.PredecessorTask)
			{
				var uid=link.PredecessorUID;
				if(!this.afterTaskLinks[uid])this.afterTaskLinks[uid]=[];
				this.afterTaskLinks[uid].push(link);
			}
		}
		
		this.readTaskLinks=function(task)
		{
			
			
			for(var link=this.adapter.readTaskFirstLink(task);link;link=this.adapter.readTaskNextLink(task,link))
			{
				this._registerLink(link);
			}
		}
		
		this.readTaskAssignments=function(task)
		{
			for(var assignment=this.adapter.readTaskFirstAssignment(task);assignment;assignment=this.adapter.readTaskNextAssignment(task,assignment))
			{
				assignment.task=task;
				this.registerAssignment(assignment);
			}
		}
		
		this.readResourceAssignments=function(resource)
		{
			for(var assignment=this.adapter.readResourceFirstAssignment(resource);assignment;assignment=this.adapter.readResourceNextAssignment(resource,assignment))
			{
				assignment.resource=resource;
				this.registerAssignment(assignment);
			}
		}
		
		_OBS_28(this,"taskregister",this,function(task)
		{
			var uid=task.UID;
			if(uid && this.afterTaskLinks && this.afterTaskLinks[uid])
			{
				var arr=this.afterTaskLinks[uid],link;
				this.afterTaskLinks[uid]=null;
				delete this.afterTaskLinks[uid];
				while(link=arr.pop())
				{
					this._registerLink(link);
				}
			}
		});
		
		_OBS_28(this,"taskunregister",this,function(element)
		{
			var links=element.PredecessorLinks;
			for(var i=links.length-1;i>=0;i--)
			{
				this.unregisterLink(links[i]);
			}
			var links=element.SuccessorLinks;
			for(var i=links.length-1;i>=0;i--)
			{
				this.unregisterLink(links[i]);
			}
			var assignments=element.Assignments;
			for(var i=assignments.length-1;i>=0;i--)
			{
				this.unregisterAssignment(assignments[i]);
			}
		});
		
		_OBS_28(this,"resourceunregister",this,function(element)
		{
			var assignments=element.Assignments;
			for(var i=assignments.length-1;i>=0;i--)
			{
				this.unregisterAssignment(assignments[i]);
			}
		});
		
		_OBS_28(this,_OBS_1[154],this,function(link)
		{
			link.SuccessorTask.PredecessorLinks.push(link);
			link.PredecessorTask.SuccessorLinks.push(link);
		});
		
		_OBS_28(this,_OBS_1[153],this,function(link)
		{
			_OBS_14(link.PredecessorTask.SuccessorLinks,link);
			_OBS_14(link.SuccessorTask.PredecessorLinks,link);
		});
		
		_OBS_28(this,_OBS_1[156],this,function(returnObj,sucTask,preTask)
		{
			if(!sucTask || !preTask){return;}
			var links=sucTask.getPredecessorLinks();
			for(var i=links.length-1;i>=0;i--)
			{
				if(links[i].PredecessorTask==preTask)
				{
					returnObj.returnValue=false;
					return;
				}
			}
			var links=sucTask.getSuccessorLinks();
			for(var i=links.length-1;i>=0;i--)
			{
				if(links[i].PredecessorTask==preTask)
				{
					returnObj.returnValue=false;
					return;
				}
			}
		});
		
		_OBS_28(this,"assignmentregister",this,function(assignment)
		{
			assignment.getTask().Assignments.push(assignment);
			if(assignment.getResource()){assignment.getResource().Assignments.push(assignment);}
		});
		
		_OBS_28(this,"assignmentunregister",this,function(assignment)
		{
			_OBS_14(assignment.task.Assignments,assignment);
			_OBS_14(assignment.resource.Assignments,assignment);
		});
		
		if(this.initComponents)
		{
			var arr=this.initComponents.split(",")
			for(var i=0;i<arr.length;i++)
			{
				this.addComponent(new window[arr[i]]());
			}
		}
	},addModule: function(type)
	{
		this.modules.push(type);
		this.elementUids[type]={};
		this["get"+type+"ByUid"]=function(uid,force){return this.getElementByUid(type,uid,force);}
		this[_OBS_1[152]+type]=function(element,property,value){return this.updateElement(type,element,property,value);}
		this[_OBS_1[151]+type]=function(element){return this.registerElement(type,element);}
		this[_OBS_1[150]+type]=function(element){return this.unregisterElement(type,element);}
		this["add"+type]=function(){var argu=[type];for(var i=0;i<arguments.length;i++){argu.push(arguments[i]);}return this.addElement(argu);}
		this["canAdd"+type]=function(){return this.checkEvent(_OBS_1[149]+type+"add",arguments);}
		this[_OBS_1[148]+type]=function(element){return this.deleteElement(type,element);}
		this["canDelete"+type]=function(){return this.checkEvent(_OBS_1[149]+type+_OBS_1[148],arguments);}
	},addTreeModule: function(type)
	{
		this.addModule(type);
		this[_OBS_1[147]+type]=function(item){return this.getRootElement(type);}
		this["read"+type+_OBS_1[146]]=function(item){return this.readElementFirstChild(type,item);}
		this["read"+type+_OBS_1[145]]=function(item){return this.readElementNextSibling(type,item);}
		this["get"+type+"ByOutline"]=function(outline){return this.getElementByOutline(type,outline);}
		this["compare"+type]=function(sItem,eItem){return this.compareElement(sItem,eItem);}
		this[_OBS_1[150]+type]=function(element){return this.unregisterTreeElement(type,element);}
		this["add"+type]=function(parent,pElement){return this.addTreeElement(type,parent,pElement);}
		this[_OBS_1[148]+type]=function(element){return this.deleteTreeElement(type,element);}
		this[_OBS_1[144]+type]=function(element,pElement,preElement){return this.moveElement(type,element,pElement,preElement);}
		this["canMove"+type]=function(element,pElement,preElement){return this.canMoveElement(type,element,pElement,preElement);}

	},getModules: function(){return this.modules;},addComponent: function(comp)
	{
		if(_OBS_13(this.components,comp)){return;}
		comp.initialize(this)
		this.components.push(comp);
	},removeComponent: function(comp)
	{
		comp.remove(comp);
		_OBS_14(this.components,comp);
	},getCalendar: function()
	{
		return this.adapter.getCalendar();
	},checkEvent: function(eventName,argu)
	{
		var en=eventName;
		var returnObj={returnValue:true};
		var eventArgu=[returnObj];
		for(var i=0;i<argu.length;i++){eventArgu.push(argu[i]);}
		_OBS_35(this,en,eventArgu);
		if(!returnObj.returnValue){return false;}
		return true;
	},depose: function()
	{
		_OBS_33(this);
		for(var key in this){this[key]=null;}
	},registerElement: function(type,item)
	{
		item.data=this;
		var uid=item.UID;
		if(uid){this.elementUids[type][uid]=item;}
		
		
		
		
		_OBS_35(this,type.toLowerCase()+_OBS_1[151],[item]);
	},unregisterElement: function(type,item)
	{
		var uid=item.UID;
		if(uid)
		{
			this.elementUids[type][uid]=null;
			delete this.elementUids[type][uid];
		}
		
		
		
		
		_OBS_35(this,type.toLowerCase()+_OBS_1[150],[item]);
		item.data=null;
	},addElement: function(type)
	{
		var argu=[];
		for(var i=1;i<arguments.length;i++){argu.push(arguments[i]);}
		if(!this.checkEvent(_OBS_1[149]+type.toLowerCase()+"add",argu)){return false;}
		var newElement=this.adapter["add"+type].apply(this.adapter,argu);
		
		
		_OBS_35(this,_OBS_1[143]+type.toLowerCase()+"add",[newElement]);
		this.registerElement(type,newElement);
		return newElement;
	},deleteElement: function(type,element)
	{
		
		
		if(!this.checkEvent(_OBS_1[149]+type.toLowerCase()+_OBS_1[148],[element])){return false;}
		this.unregisterElement(type,element);
		this.adapter[_OBS_1[148]+type](element);
		
		
		_OBS_35(this,_OBS_1[143]+type.toLowerCase()+_OBS_1[148],[element]);
		return true;
	},getRootElement: function(type)
	{
		var t=this.rootElement[type]
		if(!t)
		{
			t=this.rootElement[type]=this.adapter["readRoot"+type]();
			if(t){this.registerElement(type,t);}
		}
		return t;
	},readElementFirstChild: function(type,element)
	{
		if(!element.firstChild)
		{
			var t=element.firstChild=this.adapter["read"+type+_OBS_1[146]](element);
			if(t)
			{
				t.parent=element;
				this.registerElement(type,t);
			}
		}
		return element.firstChild;
	},readElementNextSibling: function(type,element)
	{
		if(element==this.getRootElement(type)){return null;}
		if(!element.nextSibling)
		{
			var t=element.nextSibling=this.adapter["read"+type+_OBS_1[145]](element);
			if(t)
			{
				t.previousSibling=element;
				t.parent=element.parent;
				this.registerElement(type,element.nextSibling);
			}
		}
		return element.nextSibling;
	},getElementByUid: function(type,uid,force)
	{
		var element=this.elementUids[type][uid];
		if(element || force===false){return element;}
		if(!this.lastElement[type]){this.lastElement[type]=this.getRootElement(type);}
		while(this.lastElement[type]=this.lastElement[type].getNext())
		{
			if(this.lastElement[type].UID==uid)
			{
				return this.lastElement[type];
			}
		}
		return null;
	},getElementByOutline: function(type,outline)
	{
		var element=this.getRootElement(type);
		if(!outline){return element;}
		return this.searchElementOutline(type,element,outline.split("."));
	},searchElementOutline: function(type,element,outline)
	{
		if(outline.length==0){return element}
		var child=element.getFirstChild(),index=outline.shift();
		for(var i=1;i<index;i++)
		{
			child=child.getNextSibling();
		}
		return this.searchElementOutline(type,child,outline);
	},compareElement: function(startElement,endElement)
	{
		var sArr=startElement.getOutlineNumber(this).split(".");
		var eArr=endElement.getOutlineNumber(this).split(".");
		var min=Math.min(sArr.length,eArr.length);
		for(var i=0;i<min;i++)
		{
			if(sArr[i]*1<eArr[i]*1){return 1;}
			if(sArr[i]*1>eArr[i]*1){return -1;}
		}
		if(sArr.length==eArr.length){return 0;}
		return (sArr.length<eArr.length)?1:-1;
	},updateElement: function(type,element,property,value)
	{
		this.adapter[_OBS_1[152]+type](element,property,value);
	},addTreeElement: function(type,parent,pElement)
	{
		
		
		
		
		if(!this.checkEvent(_OBS_1[149]+type.toLowerCase()+"add",[parent,pElement])){return false;}
		if(!parent.getFirstChild())
		{
			parent.setProperty(_OBS_1[142],true);
		}
		var newElement=this.adapter["add"+type](parent,pElement);
		newElement.parent=parent;
		
		if(!pElement)
		{
			newElement.previousSibling=null;
			newElement.nextSibling=parent.getFirstChild();
			if(newElement.nextSibling)
			{
				newElement.nextSibling.previousSibling=newElement;
			}
			newElement.nextSibling=parent.getFirstChild();
			parent.firstChild=newElement;
		}
		else
		{
			newElement.previousSibling=pElement;
			newElement.nextSibling=pElement.getNextSibling();
			if(newElement.nextSibling)
			{
				newElement.nextSibling.previousSibling=newElement;
			}
			pElement.nextSibling=newElement;
		}
		this.registerElement(type,newElement);
		
		
		_OBS_35(this,_OBS_1[143]+type.toLowerCase()+"add",[newElement]);
		return newElement;
	},deleteTreeElement: function(type,element)
	{
		
		
		if(!this.checkEvent(_OBS_1[149]+type.toLowerCase()+_OBS_1[148],[element])){return false;}
		var parent=element.getParent(),pt=element.getPreviousSibling(),nt=element.getNextSibling();
		if(pt){pt.nextSibling=nt;}
		if(nt){nt.previousSibling=pt;}
		if(parent)
		{
			if(parent.getFirstChild()==element){parent.firstChild=nt;}
			parent.setProperty(_OBS_1[142],!!parent.getFirstChild());
		}
		element.previousSibling=null;
		element.nextSibling=null;
		this.adapter[_OBS_1[148]+type](element);
		this.unregisterTreeElement(type,element)
		
		
		_OBS_35(this,_OBS_1[143]+type.toLowerCase()+_OBS_1[148],[element,parent,pt]);
		return true;
	},unregisterTreeElement: function(type,element)
	{
		var child=element.firstChild;
		element.firstChild=null;
		while(child)
		{
			this.unregisterTreeElement(type,child);
			var c=child.nextSibling;
			child.nextSibling=null;
			child.previousSibling=null;
			child.parent=null;
			child=c;
		}
		this.unregisterElement(type,element);
	},moveElement: function(type,element,pElement,preElement)
	{
		if(!this.canMoveElement(type,element,pElement,preElement)){return false;}
		
		var parent=element.getParent(),previousSibling=element.getPreviousSibling(),nextSibling=element.getNextSibling();
		if(parent.getFirstChild()==element)
		{
			parent.firstChild=nextSibling;
			if(!nextSibling){parent.setProperty(_OBS_1[142],false);}
		}
		element.parent=null;
		if(previousSibling)
		{
			previousSibling.nextSibling=nextSibling;
			element.previousSibling=null;
		}
		if(nextSibling)
		{
			nextSibling.previousSibling=previousSibling;
			element.nextSibling=null;
		}
		
		element.parent=pElement;
		element.previousSibling=preElement;
		if(preElement)
		{
			element.nextSibling=preElement.getNextSibling();
			preElement.nextSibling=element;
		}
		else
		{
			element.nextSibling=pElement.getFirstChild();
			pElement.firstChild=element;
		}
		if(element.nextSibling){element.nextSibling.previousSibling=element;}
		
		pElement.setProperty(_OBS_1[142],true);
		this.adapter[_OBS_1[144]+type](element,parent,previousSibling);
		
		
		_OBS_35(this,_OBS_1[143]+type.toLowerCase()+_OBS_1[144],[element,parent,previousSibling]);
		return true;
	},canMoveElement: function(type,element,pElement,preElement)
	{
		if(!pElement && preElement){pElement=preElement.getParent();}
		if(!pElement){pElement=this.getRootElement(type);}
		if(preElement && preElement.getParent()!=pElement)
		{
			return false;
		}
		
		if(element.contains(pElement))
		{
			return false;
		}
		
		
		
		if(!this.checkEvent(_OBS_1[149]+type.toLowerCase()+_OBS_1[144],[element,pElement,preElement])){return false;}
		return true;
	}});
	 function SFDataElement(){}
	_OBS_4(SFDataElement.prototype,{getProperty: function(name){return this[name];},setProperty: function(name,value)
	{
		var a=(typeof(this[name])==_OBS_1[184] && value)?this[name].valueOf():this[name];
		var b=(typeof(value)==_OBS_1[184] && value)?value.valueOf():value;
		if(a==b){return true;}
		if(!this.canSetProperty(name,value)){return false;}
		var beforeValue=this[name];
		this[name]=value;
		if(!this.data){return true}
		if(this.data[_OBS_1[152]+this.elementType]){this.data[_OBS_1[152]+this.elementType](this,name,value);}
		 
		 if(name=="UID")
		 {
		 	 var uids=this.data.elementUids[this.elementType];
		 	 if(beforeValue){delete uids[beforeValue];}
		 	 if(value){uids[value]=this;}
		 }
		_OBS_35(this.data,_OBS_1[143]+this.elementType.toLowerCase()+_OBS_1[141],[this,name,value,beforeValue]);
		var bp={},ap={};
		bp[name]=beforeValue;
		ap[name]=value
		
		
		
		
		_OBS_35(this.data,_OBS_1[143]+this.elementType.toLowerCase()+_OBS_1[152],[this,[name],ap,bp]);
		return true;
	},canSetProperty: function(name,value)
	{
		
		if(!this.data){return true;}
		
		
		
		
		return this.data.checkEvent(_OBS_1[149]+this.elementType.toLowerCase()+_OBS_1[141],[this,name,value]);
	}});
	 function SFDataTreeElement(){}
	SFDataTreeElement.prototype=new SFDataElement()
	_OBS_4(SFDataTreeElement.prototype,{getFirstChild: function()
	{
		if(typeof(this.firstChild)==_OBS_1[174])
		{
			this.firstChild=this.data.readElementFirstChild(this.elementType,this);
		}
		return this.firstChild;
	},getParent: function()
	{
		return this.parent;
	},getPreviousSibling: function()
	{
		return this.previousSibling;
	},getNextSibling: function(autoUp)
	{
		if(typeof(this.nextSibling)==_OBS_1[174])
		{
			this.nextSibling=this.data.readElementNextSibling(this.elementType,this);
		}
		if(!this.nextSibling && autoUp)
		{
			var parent=this.getParent();
			if(parent)
			{
				return parent.getNextSibling(autoUp);
			}
		}
		return this.nextSibling;
	},getAncestor: function(level)
	{
		var cLevel=this.getOutlineLevel();
		var element=this;
		while(cLevel>level)
		{
			element=element.getParent();
			cLevel--;
		}
		return element;
	},getPrevious: function()
	{
		var element=this.getPreviousSibling();
		return element?element.getLastDescendant():this.getParent();
	},getNext: function()
	{
		if(this==this.data.getRootElement(this.elementType)){return this.getFirstChild();}
		if(this.Summary)
		{
			var element=this.getFirstChild();
			if(element){return element;}
		}
		var element=this.getNextSibling();
		if(element){return element;}
		
		for(var pElement=this.getParent();pElement;pElement=pElement.getParent())
		{
			element=pElement.getNextSibling();
			if(element){return element;}
		}
		return null;
	},getLastChild: function()
	{
		var lastChild=null;
		for(var child=this.getFirstChild();child;child=child.getNextSibling())
		{
			lastChild=child;
		}
		return lastChild;
	},getLastDescendant: function(onlyView)
	{
		if(!this.Summary || (onlyView && this.Collapse)){return this;}
		var lastChild=this.getLastChild();
		return lastChild?lastChild.getLastDescendant(onlyView):this;
	},getNextView: function()
	{
	    return this.Collapse?this.getNextSibling(true):this.getNext();
	},getPreviousView: function()
	{
		var t=this.getPreviousSibling();
		if (t){return t.getLastDescendant(true)}
		t=this.getParent();
		if(t && t.getOutlineLevel()>0){return t;}
		return null;
	},isHidden: function()
	{
		if(!this.data){return true;}
		for(var t=this.getParentTask();t;t=t.getParentTask())
		{
			if(t.Collapse || !t.data)
			{
				return true;
			}
		}
		return false;
	},contains: function(element)
	{
		for(var p=element;p;p=p.getParent())
		{
			if(p==this){return true;}
		}
		return false;
	},getSiblingIndex: function()
	{
		var index=0,c=this;
		while(c)
		{
			c=c.getPreviousSibling();
			index++;
		}
		return index;
	},getOutlineNumber: function(data)
	{
		data=data?data:this.data;
		var t=this,root=data.getRootElement(this.elementType);
		if(t==root){return '0'}
		var arr=[];
		while (t && t!=root)
		{
			arr.unshift(t.getSiblingIndex())
			t=t.getParent()
		}
		return arr.join(".")
	},getOutlineLevel: function()
	{
		var t=this,num=-1;
		while (t)
		{
			num++
			t=t.getParent()
		}
		return num
	}});
	 function SFDataTask()
	{
		this.elementType=_OBS_1[160];
		_OBS_4(this,{SuccessorLinks:[],PredecessorLinks:[],Assignments:[]});
		_OBS_4(this,{getParentTask:this.getParent,getNextTask:this.getNext,getPreviousTask:this.getPrevious,getAncestorTask:this.getAncestor,getNextViewTask:this.getNextView,getPreviousViewTask:this.getPreviousView,containsTask:this.contains});
	}
	SFDataTask.prototype=new SFDataElement()
	_OBS_4(SFDataTask.prototype,{getOutlineLevel: function()
	{
		var t=this,num=-1;
		while (t)
		{
			num++
			t=t.getParent()
		}
		return num
	},getOutlineNumber: function(data)
	{
		data=data?data:this.data;
		var t=this,root=data.getRootElement(this.elementType);
		if(t==root){return '0'}
		var arr=[];
		while (t && t!=root)
		{
			arr.unshift(t.getSiblingIndex())
			t=t.getParent()
		}
		return arr.join(".")
	},getSiblingIndex: function()
	{
		var index=0,c=this;
		while(c)
		{
			c=c.getPreviousSibling();
			index++;
		}
		return index;
	},contains: function(element)
	{
		for(var p=element;p;p=p.getParent())
		{
			if(p==this){return true;}
		}
		return false;
	},isHidden: function()
	{
		if(!this.data){return true;}
		for(var t=this.getParentTask();t;t=t.getParentTask())
		{
			if(t.Collapse || !t.data)
			{
				return true;
			}
		}
		return false;
	},getPreviousView: function()
	{
		var t=this.getPreviousSibling();
		if (t){return t.getLastDescendant(true)}
		t=this.getParent();
		if(t && t.getOutlineLevel()>0){return t;}
		return null;
	},getNextView: function()
	{
	    return this.Collapse?this.getNextSibling(true):this.getNext();
	},getLastDescendant: function(onlyView)
	{
		if(!this.Summary || (onlyView && this.Collapse)){return this;}
		var lastChild=this.getLastChild();
		return lastChild?lastChild.getLastDescendant(onlyView):this;
	},getLastChild: function()
	{
		var lastChild=null;
		for(var child=this.getFirstChild();child;child=child.getNextSibling())
		{
			lastChild=child;
		}
		return lastChild;
	},getNext: function()
	{
		if(this==this.data.getRootElement(this.elementType)){return this.getFirstChild();}
		if(this.Summary)
		{
			var element=this.getFirstChild();
			if(element){return element;}
		}
		var element=this.getNextSibling();
		if(element){return element;}
		
		for(var pElement=this.getParent();pElement;pElement=pElement.getParent())
		{
			element=pElement.getNextSibling();
			if(element){return element;}
		}
		return null;
	},getPrevious: function()
	{
		var element=this.getPreviousSibling();
		return element?element.getLastDescendant():this.getParent();
	},getAncestor: function(level)
	{
		var cLevel=this.getOutlineLevel();
		var element=this;
		while(cLevel>level)
		{
			element=element.getParent();
			cLevel--;
		}
		return element;
	},getNextSibling: function(autoUp)
	{
		if(typeof(this.nextSibling)==_OBS_1[174])
		{
			this.nextSibling=this.data.readElementNextSibling(this.elementType,this);
		}
		if(!this.nextSibling && autoUp)
		{
			var parent=this.getParent();
			if(parent)
			{
				return parent.getNextSibling(autoUp);
			}
		}
		return this.nextSibling;
	},getPreviousSibling: function()
	{
		return this.previousSibling;
	},getParent: function()
	{
		return this.parent;
	},getFirstChild: function()
	{
		if(typeof(this.firstChild)==_OBS_1[174])
		{
			this.firstChild=this.data.readElementFirstChild(this.elementType,this);
		}
		return this.firstChild;
	},update: function(){},checkTime: function()
	{
		var startDate=Number.MAX_VALUE,endDate=Number.MIN_VALUE;
		for(var child=this.getFirstChild();child;child=child.getNextSibling())
		{
			if(child.Start){startDate=Math.min(startDate,child.Start.valueOf());}
			if(child.Finish){endDate=Math.max(endDate,child.Finish.valueOf());}
		}
		if(startDate==Number.MAX_VALUE)
		{
			this.setProperty(_OBS_1[140],this.Start);
		}
		else
		{
			this.setProperty(_OBS_1[139],new Date(startDate));
			this.setProperty(_OBS_1[140],new Date(Math.max(startDate,endDate)));
		}
	},getPredecessorLinks: function()
	{
		if(!this.linksRead)
		{
			this.data.readTaskLinks(this);
			this.linksRead=true;
		}
		return this.PredecessorLinks;
	},getSuccessorLinks: function()
	{
		if(!this.linksRead)
		{
			this.data.readTaskLinks(this);
			this.linksRead=true;
		}
		return this.SuccessorLinks;
	},getPredecessorTasks: function()
	{
		var tasks=[],links=this.getPredecessorLinks();
		for(var i=0;i<links.length;i++)
		{
			tasks.push(links[i].getPredecessorTask());
		}
		return tasks;
	},getSuccessorTasks: function()
	{
		var tasks=[],links=this.getSuccessorLinks();
		for(var i=0;i<links.length;i++)
		{
			tasks.push(links[i].getSuccessorTask());
		}
		return tasks;
	},getAssignments: function()
	{
		if(this.Summary){return [];}
		if(!this.assignmentsRead)
		{
			this.data.readTaskAssignments(this);
			this.assignmentsRead=true;
		}
		return this.Assignments;
	},addPredecessorLink: function(objTask,type)
	{
		var link=this.data.addLink(this,objTask,type);
		return link;
	},addSuccessorLink: function(objTask,type)
	{
		var link=this.data.addLink(objTask,this,type);
		return link;
	},addAssignment: function(resource,unit)
	{
		var am=this.data.addAssignment(this,resource,unit);
		if(!am){return;}
		if(unit){am.setProperty(_OBS_1[138],unit);}
		return am;
	}});
	 function SFDataLink()
	{
		this.elementType=_OBS_1[158];
	}
	SFDataLink.prototype=new SFDataElement()
	_OBS_4(SFDataLink.prototype,{getPredecessorTask: function()
	{
		return this.PredecessorTask;
	},getSuccessorTask: function()
	{
		return this.SuccessorTask;
	}});
	 function SFDataAssignment()
	{
		this.elementType=_OBS_1[157];
	}
	SFDataAssignment.prototype=new SFDataElement()
	_OBS_4(SFDataAssignment.prototype,{getTask: function()
	{
		return this.task?this.task:this.data.getTaskByUid(this.TaskUID);
	},getResource: function()
	{
		return this.resource?this.resource:this.data.getResourceByUid(this.ResourceUID);
	}});
	 function SFDataResource()
	{
		this.elementType=_OBS_1[159];
		_OBS_4(this,{getParentResource:this.getParent,getNextResource:this.getNext,getPreviousResource:this.getPrevious,getAncestorResource:this.getAncestor,getNextViewResource:this.getNextView,getPreviousViewResource:this.getPreviousView,containsResource:this.contains});
		this.Assignments=[];
	}
	SFDataResource.prototype=new SFDataTreeElement()
	_OBS_4(SFDataResource.prototype,{getAssignments: function()
	{
		if(!this.assignmentsRead)
		{
			this.data.readResourceAssignments(this);
			this.assignmentsRead=true;
		}
		return this.Assignments;
	},addAssignment: function(task,unit)
	{
		var am=this.data.addAssignment(task,this);
		if(!am){return;}
		if(unit){am.setProperty(_OBS_1[138],unit);}
		return am;
	}});
	 function SFDataRender(readFunc,writeFunc)
	{
		this.read=readFunc;
		this.write=writeFunc;
	}
	 function _OBS_64(node)
	{
		return this.read.apply(this,[node]);
	}
	 function _OBS_65(node,value)
	{
		return this.write.apply(this,[node,value]);
	}
	 function _OBS_66()
	{
		

		SFDataRender.types={
			
			Bool2Int:	new SFDataRender(_OBS_74,	_OBS_75),
			
			Int:		new SFDataRender(_OBS_72,		_OBS_73),
			
			Float:		new SFDataRender(_OBS_76,	_OBS_77),
			
			String:		new SFDataRender(_OBS_70,	_OBS_71),
			
			Time:		new SFDataRender(_OBS_68,		_OBS_69)
		};
	}
	 function _OBS_67(name)
	{
		return SFDataRender.types[name];
	}
	 function _OBS_68(node)
	{
		return _OBS_7(_OBS_47(node));
	}
	 function _OBS_69(node,value)
	{
		_OBS_48(node,_OBS_9(value,'s'));
	}
	 function _OBS_70(node)
	{
		return _OBS_47(node);
	}
	 function _OBS_71(node,value)
	{
		_OBS_48(node,value);
	}
	 function _OBS_72(node)
	{
		return parseInt(_OBS_47(node));
	}
	 function _OBS_73(node,value)
	{
		_OBS_48(node,parseInt(value));
	}
	 function _OBS_74(node)
	{
		return parseInt(_OBS_47(node))>0?true:false;
	}
	 function _OBS_75(node,value)
	{
		_OBS_48(node,value?1:0);
	}
	 function _OBS_76(node)
	{
		return parseFloat(_OBS_47(node));
	}
	 function _OBS_77(node,value)
	{
		_OBS_48(node,parseFloat(value));
	}
	_OBS_4(SFDataRender,{read:_OBS_64,write:_OBS_65,init:_OBS_66,getType:_OBS_67,TimeRead:_OBS_68,TimeWrite:_OBS_69,StringRead:_OBS_70,StringWrite:_OBS_71,IntRead:_OBS_72,IntWrite:_OBS_73,Bool2IntRead:_OBS_74,Bool2IntWrite:_OBS_75,FloatRead:_OBS_76,FloatWrite:_OBS_77});
	 function SFDataAdapter(){}
	_OBS_4(SFDataAdapter.prototype,{initialize: function(){},remove: function(){},depose: function()
	{
		if(this.listeners)
		{
			var listenr;
			while(listenr=this.listeners.pop()){_OBS_32(listenr)}
		}
	},getCalendar: function(){return _OBS_56(_OBS_1[173]);},readRootTask: function(){},readTaskFirstChild: function(){},readTaskNextSibling: function(){},readRootResource: function(){},readResourceFirstChild: function(){},readResourceNextSibling: function(){},readTaskFirstLink: function(){},readTaskNextLink: function(){},readTaskFirstAssignment: function(){},readTaskNextAssignment: function(){},readResourceFirstAssignment: function(){},readResourceNextAssignment: function(){},updateTask: function(){},addTask: function(){return new SFDataTask();},deleteTask: function(){},moveTask: function(){},updateResource: function(){},addResource: function(){return new SFDataResource();},deleteResource: function(){},moveResource: function(){},updateLink: function(){},addLink: function(){return new SFDataLink();},deleteLink: function(){},updateAssignment: function(){},addAssignment: function(){return new SFDataAssignment();},deleteAssignment: function(){}});
	 function SFDataXmlBase()
	{}
	SFDataXmlBase.prototype=new SFDataAdapter()
	_OBS_4(SFDataXmlBase.prototype,{initialize: function()
	{
		SFDataAdapter.prototype.initialize.apply(this,arguments);
	},getConfig: function()
	{
		return this.config;
	},getXml: function()
	{
		return this.doc;
	},readCalendar: function(calNode)
	{
		var wds=new Array(7),exceptions=[];
		var wdsNode=_OBS_46(calNode,"WeekDays");
		for(var wdNode=wdsNode.firstChild;wdNode;wdNode=wdNode.nextSibling)
		{
			if(wdNode.nodeName!="WeekDay"){continue;}
			var dayType=parseInt(_OBS_47(_OBS_46(wdNode,"DayType")));
			var dayWorking=parseInt(_OBS_47(_OBS_46(wdNode,"DayWorking")));
			var workTime=this.getCalendarTime(_OBS_46(wdNode,"WorkingTimes"));
			if(dayType)
			{
				wds[dayType-1]=workTime;
			}
			else
			{
				exceptions.push([_OBS_7(_OBS_47(_OBS_46(wdNode,"TimePeriod/FromDate"))),_OBS_7(_OBS_47(_OBS_46(wdNode,"TimePeriod/ToDate"))),workTime]);
			}
		}
		return new SFWorkingCalendar(_OBS_57(wds,exceptions));
	},getCalendarTime: function(wtsNode)
	{
		var wts=[];
		if(!wtsNode){return wts;}
		for(var wtNode=wtsNode.firstChild;wtNode;wtNode=wtNode.nextSibling)
		{
			if(wtNode.nodeName!="WorkingTime"){continue;}
			wts.push([this.getMinutes(_OBS_47(_OBS_46(wtNode,"FromTime"))),this.getMinutes(_OBS_47(_OBS_46(wtNode,"ToTime")))]);
		}
		return wts;
	},getMinutes: function(string)
	{
		var timeReg=new RegExp("^([0-9]+):([0-9]+):([0-9]+)$");
		var result=timeReg.exec(string);
		return parseInt(result[1],10)*60+parseInt(result[2],10)+parseInt(result[3],10)/60;
	},addDefaultProperty: function()
	{
		var renderType=SFDataRender.types;
		
		this.addTaskProperty("UID",		0,			renderType.String);
		
		this.addTaskProperty(_OBS_1[142],		0,			renderType.Bool2Int);


		
		this.addTaskProperty('ID',		0,			renderType.Int);
		
		this.addTaskProperty(_OBS_1[137],	0,			renderType.String);
		
		this.addTaskProperty(_OBS_1[136],	0,			renderType.Int);
		
		this.addTaskProperty(_OBS_1[139],		0,			renderType.Time);
		
		this.addTaskProperty(_OBS_1[140],		0,			renderType.Time);
		
		this.addTaskProperty('Name',		0,			renderType.String);
		
		this.addTaskProperty(_OBS_1[135],	0,			renderType.Bool2Int);


		
		this.addTaskProperty(_OBS_1[134],	0,			renderType.Int);
		
		this.addTaskProperty(_OBS_1[133],		0,			renderType.String);
		
		this.addTaskProperty(_OBS_1[132],	0,			renderType.Int);
		
		this.addTaskProperty(_OBS_1[131],	0,			renderType.Time);
		
		this.addTaskProperty(_OBS_1[130],	0,			renderType.Time);
		
		this.addTaskProperty(_OBS_1[129],	0,			renderType.Time);
		
		this.addTaskProperty('Hyperlink',	0,			renderType.String);
		
		this.addTaskProperty('HyperlinkAddress',0,			renderType.String);


		
		this.addTaskProperty(_OBS_1[128],	0,			renderType.String);
		
		this.addTaskProperty(_OBS_1[127],	0,			renderType.Bool2Int);
		
		this.addTaskProperty(_OBS_1[126],	0,			renderType.Int);
		
		this.addTaskProperty(_OBS_1[125],	0,			renderType.Bool2Int);


		
		this.addTaskProperty(_OBS_1[124],	'Baseline/Start',	renderType.Time);
		
		this.addTaskProperty(_OBS_1[123],	'Baseline/Finish',	renderType.Time);


		
		this.addResourceProperty("UID",		0,			renderType.String);
		
		this.addResourceProperty(_OBS_1[142],	0,			renderType.Bool2Int);
		
		this.addResourceProperty(_OBS_1[127],	0,			renderType.Bool2Int);
		
		this.addResourceProperty('Name',	0,			renderType.String);
		
		this.addResourceProperty('ID',		0,			renderType.Int);
		
		this.addResourceProperty(_OBS_1[137],0,			renderType.String);
		
		this.addResourceProperty(_OBS_1[136],0,			renderType.Int);
		
		this.addResourceProperty(_OBS_1[135],	0,			renderType.Bool2Int);
		
		this.addResourceProperty(_OBS_1[133],	0,			renderType.String);


		
		this.addLinkProperty("UID",		0,			renderType.String);
		
		this.addLinkProperty(_OBS_1[122],	0,			renderType.String);
		
		this.addLinkProperty(_OBS_1[121],	0,			renderType.String);
		
		this.addLinkProperty(_OBS_1[120],		0,			renderType.Int);


		
		this.addAssignmentProperty("UID",	0,			renderType.String);
		
		this.addAssignmentProperty(_OBS_1[119],	0,			renderType.String);
		
		this.addAssignmentProperty(_OBS_1[118],0,			renderType.String);
		
		this.addAssignmentProperty(_OBS_1[138],	0,			renderType.Float);
	},addTaskProperty: function(proName,tagName,type)
	{
		tagName=tagName?tagName:proName
		var obj={proName:proName,tagName:tagName,type:type};
		this.taskReader[tagName]=obj;
		this.taskWriter[proName]=obj;
		if(tagName.indexOf("/")>0)
		{
			var name=tagName.split("/")[0];
			if(!this.taskReader[name])
			{
				this.taskReader[name]=[];
			}
			this.taskReader[name].push(obj);
		}
	},addResourceProperty: function(proName,tagName,type)
	{
		tagName=tagName?tagName:proName
		var obj={proName:proName,tagName:tagName,type:type};
		this.resourceReader[tagName]=obj;
		this.resourceWriter[proName]=obj;
	},addLinkProperty: function(proName,tagName,type)
	{
		tagName=tagName?tagName:proName
		var obj={proName:proName,tagName:tagName,type:type};
		this.linkReader[tagName]=obj;
		this.linkWriter[proName]=obj;
	},addAssignmentProperty: function(proName,tagName,type)
	{
		tagName=tagName?tagName:proName
		var obj={proName:proName,tagName:tagName,type:type};
		this.assignmentReader[tagName]=obj;
		this.assignmentWriter[proName]=obj;
	},addExtendedAttributes: function(node)
	{
		if(!this.extendedAttributes){this.extendedAttributes={};}
		var FieldID,FieldName;
		for(var child=node.firstChild;child;child=child.nextSibling)
		{
			switch(child.nodeName)
			{
				case _OBS_1[117]:
				case "FieldName":
					FieldName=_OBS_47(child);
					break;
			}
		}
		this.extendedAttributes[FieldID]={FieldID:FieldID,FieldName:FieldName};
	},readTask: function(node)
	{
		if(!node){return null;}
		var task=new SFDataTask();
		task.node=node;
		var reader=this.taskReader;
		for(var child=node.firstChild;child;child=child.nextSibling)
		{
			switch(child.nodeName)
			{
				case _OBS_1[116]:
					for(var c=child.firstChild;c;c=c.nextSibling)
					{
						var FieldID,Value;
						switch(c.nodeName)
						{
							case _OBS_1[117]:
								FieldID=_OBS_47(c);
								break;
							case "Value":
								Value=_OBS_47(c);
								break;
						}
					}
					task[FieldID]=Value;
					
					break;
				default:
					var property=reader[child.nodeName];
					if(property)
					{
						if(property.length)
						{
							for(var c=child.firstChild;c;c=c.nextSibling)
							{
								if(c.nodeName.indexOf("#")==0){continue;}
								var pro=reader[child.nodeName+"/"+c.nodeName];
								if(pro)
								{
								    task[pro.proName]=_OBS_64.apply(pro.type,[c]);
									
								}
							}
						}
						else
						{
						    task[property.proName]=_OBS_64.apply(property.type,[child]);
							
						}
					}
					break;
			}
		}
		this.taskCount++;
		if(task.OutlineNumber)
		{
			if(!task.OutlineLevel)
			{
				task.OutlineLevel=task.OutlineNumber=='0'?0:task.OutlineNumber.split(".").length;
			}
			task.OriginalLevel=task.OutlineLevel;
		}
		return task;
	},readResource: function(node)
	{
		if(!node){return null;}
		var resource=new SFDataResource();
		resource.node=node;
		var reader=this.resourceReader;
		for(var child=node.firstChild;child;child=child.nextSibling)
		{
			var property=reader[child.nodeName];
			if(property)
			{
				resource[property.proName]=_OBS_64.apply(property.type,[child]);
				
			}
		}
		if(resource.OutlineNumber)
		{
			if(!resource.OutlineLevel)
			{
				resource.OutlineLevel=resource.OutlineNumber=='0'?0:resource.OutlineNumber.split(".").length;
			}
			resource.OriginalLevel=resource.OutlineLevel;
		}
		return resource;
	},readLink: function(node)
	{
		if(!node){return null;}
		var link=new SFDataLink();
		link.node=node;
		var reader=this.linkReader;
		for(var child=node.firstChild;child;child=child.nextSibling)
		{
			var property=reader[child.nodeName];
			if(property)
			{
				link[property.proName]=_OBS_64.apply(property.type,[child]);
			}
		}
		return link;
	},readAssignment: function(node)
	{
		if(!node){return null;}
		var assignment=new SFDataAssignment();
		assignment.node=node;
		var reader=this.assignmentReader;
		for(var child=node.firstChild;child;child=child.nextSibling)
		{
			var property=reader[child.nodeName];
			if(property)
			{
				assignment[property.proName]=_OBS_64.apply(property.type,[child]);
			}
		}
		return assignment;
	},readTaskLink: function(task,node)
	{
		if(!node){return null;}
		var link=this.readLink(node);
		link[node.nodeName==_OBS_1[115]?_OBS_1[114]:_OBS_1[113]]=task;
		return link;
	},readTaskAssignment: function(task,node)
	{
		if(!node){return null;}
		var assignment=this.readAssignment(node);
		assignment.task=task;
		return assignment;
	},readResourceAssignment: function(resource,node)
	{
		if(!node){return null;}
		var assignment=this.readAssignment(node);
		assignment.resource=resource;
		return assignment;
	},updateItem: function(writer,item,proName,value)
	{
		var property=writer[proName];
		if(property)
		{
			var node=_OBS_46(item.node,property.tagName);
			if(!node)
			{
				var names=property.tagName.split("/"),pNode=item.node;
				for(var i=0;i<names.length;i++)
				{
					if(!names[i]){continue;}
					node=_OBS_46(pNode,names[i]);
					if(!node)
					{
						node=pNode.ownerDocument.createElement(names[i]);
						pNode.appendChild(node);
					}
					pNode=node;
				}
			}
			_OBS_65.apply(property.type,[node,value])
		}
		if(!writer[proName] && this.extendedAttributes && this.extendedAttributes[proName])
		{
			for(var child=item.node.firstChild;child;child=child.nextSibling)
			{
				if(child.nodeName!=_OBS_1[116]){continue;}
				var idNode=_OBS_46(child,_OBS_1[117]);
				if(!idNode || _OBS_47(idNode)!=proName){continue;}
				var valueNode=_OBS_46(child,"Value");
				if(!valueNode)
				{
					valueNode=child.ownerDocument.createElement("Value");
					child.appendChild(valueNode);
				}
				_OBS_65.apply(SFDataRender.types.String,[valueNode,value]);
				return;
			}
			var child=item.node.ownerDocument.createElement(_OBS_1[116]);
			var idNode=child.ownerDocument.createElement(_OBS_1[117]);
			_OBS_65.apply(SFDataRender.types.String,[idNode,proName]);
			child.appendChild(idNode);
			var valueNode=child.ownerDocument.createElement("Value");
			_OBS_65.apply(SFDataRender.types.String,[valueNode,value]);
			child.appendChild(valueNode);
		}
	},updateTask: function(task,proName,value)
	{
		if(!this.saveChange){return;}
		this.updateItem(this.taskWriter,task,proName,value);
	},updateLink: function(link,proName,value)
	{
		if(!this.saveChange){return;}
		this.updateItem(this.linkWriter,link,proName,value);
	},updateResource: function(resource,proName,value)
	{
		if(!this.saveChange){return;}
		this.updateItem(this.resourceWriter,resource,proName,value);
	},updateAssignment: function(assignment,proName,value)
	{
		if(!this.saveChange){return;}
		this.updateItem(this.assignmentWriter,assignment,proName,value);
	}});
	 function SFDataXml(url,config)
	{
		_OBS_4(this,{taskReader:{},taskWriter:{},resourceReader:{},resourceWriter:{},linkReader:{},linkWriter:{},assignmentReader:{},assignmentWriter:{}});
		var doc=(typeof(url)==_OBS_1[181])?this.loadUrl(url):url;
		config=config?config:new SFConfig();
		
		_OBS_53(this,config.getConfigObj("SFDataXml"));
		_OBS_4(this,{doc:doc,config:config});
		this.addDefaultProperty();
	}
	SFDataXml.prototype=new SFDataXmlBase()
	_OBS_4(SFDataXml.prototype,{initialize: function()
	{
		SFDataXmlBase.prototype.initialize.apply(this,arguments);
	},loadUrl: function(url)
	{
		var doc;
		function onXmlLoad(d){doc=d;}
		_OBS_44(url,onXmlLoad,false);
		return doc;
	},getCalendar: function()
	{
		var calId;
		var node=_OBS_46(this.doc.documentElement,_OBS_1[111]);
		if(node)
		{
			calId=_OBS_47(node);
			var calsNode=_OBS_46(this.doc.documentElement,"Calendars");
			for(var child=calsNode.firstChild;child;child=child.nextSibling)
			{
				if(child.nodeName!="Calendar"){continue;}
				if(_OBS_47(_OBS_46(child,"UID"))==calId)
				{
					return this.readCalendar(child); 
				}
			}
		}
		return _OBS_56(_OBS_1[173]);
	},readRootTask: function()
	{
		var rootTaskNode=_OBS_46(this.doc.documentElement,_OBS_1[160]);
		if(!rootTaskNode)
		{
			var task=this.addTask();
			return task;
		}
		return this.readTask(rootTaskNode);
	},readTaskFirstChild: function(task)
	{
		if(!task.node){return null;}
		if(task.node.getAttribute(_OBS_1[110]))
		{
			var doc=this.loadUrl(task.node.getAttribute(_OBS_1[110]));
			task.node.removeAttribute(_OBS_1[110]);
			var tasksNode=_OBS_46(task.node,_OBS_1[109]);
			if(!tasksNode)
			{
				tasksNode=task.node.ownerDocument.createElement(_OBS_1[109]);
				task.node.appendChild(tasksNode);
			}
			while(doc.documentElement.firstChild)
			{
				var taskNode=doc.documentElement.firstChild;
				doc.documentElement.removeChild(taskNode);
				tasksNode.appendChild(taskNode);
			}
		}
		return this.readTask(_OBS_46(task.node,"Tasks/Task"));
	},readTaskNextSibling: function(task)
	{
		if(!task.node){return null;}
		if(task.node.getAttribute(_OBS_1[108]))
		{
			var doc=this.loadUrl(task.node.getAttribute(_OBS_1[108]));
			task.node.removeAttribute(_OBS_1[108]);
			var tasksNode=_OBS_46(task.getParentTask().node,_OBS_1[109]);
			while(doc.documentElement.firstChild)
			{
				var taskNode=doc.documentElement.firstChild;
				doc.documentElement.removeChild(taskNode);
				tasksNode.appendChild(taskNode);
			}
		}
		return this.readTask(_OBS_50(task.node.nextSibling,_OBS_1[160]));
	},readRootResource: function()
	{
		var rootResourceNode=_OBS_46(this.doc.documentElement,_OBS_1[159]);
		if(!rootResourceNode)
		{
			var resource=this.addResource();
			return resource;
		}
		return this.readResource(rootResourceNode);
	},readResourceFirstChild: function(resource)
	{
		if(!resource.node){return null;}
		if(resource.node.getAttribute(_OBS_1[110]))
		{
			var doc=this.loadUrl(resource.node.getAttribute(_OBS_1[110]));
			resource.node.removeAttribute(_OBS_1[110]);
			var resourcesNode=_OBS_46(resource.node,_OBS_1[107]);
			if(!resourcesNode)
			{
				resourcesNode=resource.node.ownerDocument.createElement(_OBS_1[107]);
				resource.node.appendChild(resourcesNode);
			}
			while(doc.documentElement.firstChild)
			{
				var resourceNode=doc.documentElement.firstChild;
				doc.documentElement.removeChild(resourceNode);
				resourcesNode.appendChild(resourceNode);
			}
		}
		return this.readResource(_OBS_46(resource.node,"Resources/Resource"));
	},readResourceNextSibling: function(resource)
	{
		if(!resource.node){return null;}
		if(resource.node.getAttribute(_OBS_1[108]))
		{
			var doc=this.loadUrl(resource.node.getAttribute(_OBS_1[108]));
			resource.node.removeAttribute(_OBS_1[108]);
			var resourcesNode=_OBS_46(resource.getParentResource().node,_OBS_1[107]);
			while(doc.documentElement.firstChild)
			{
				var resourceNode=doc.documentElement.firstChild;
				doc.documentElement.removeChild(resourceNode);
				resourcesNode.appendChild(resourceNode);
			}
		}
		return this.readResource(_OBS_50(resource.node.nextSibling,_OBS_1[159]));
	},readTaskFirstLink: function(task)
	{
		var node,taskNode=task.node;
		if(!taskNode){return null;}
		for(node=taskNode.firstChild;node;node=node.nextSibling){if(node.nodeName==_OBS_1[115] || node.nodeName==_OBS_1[106]){break;}}
		if(node==null){node=_OBS_46(taskNode,"Links/*");}
		return this.readTaskLink(task,node);
	},readTaskNextLink: function(task,link)
	{
		var node,linkNode=link.node;
		if(!linkNode){return null;}
		for(node=linkNode.nextSibling;node;node=node.nextSibling){if(node.nodeName==_OBS_1[115] || node.nodeName==_OBS_1[106]){break;}}
		if(!node && linkNode.parentNode.nodeName!="Links")
		{
			node=_OBS_46(linkNode,_OBS_1[105]);
		}
		return this.readTaskLink(task,node);
	},readTaskFirstAssignment: function(task)
	{
		if(!task.node){return null;}
		return this.readTaskAssignment(task,_OBS_46(task.node,_OBS_1[104]));
	},readTaskNextAssignment: function(task,assignment)
	{
		if(!assignment.node){return null;}
		return this.readTaskAssignment(task,_OBS_50(assignment.node.nextSibling,_OBS_1[157]))
	},readResourceFirstAssignment: function(resource)
	{
		if(!resource.node){return null;}
		return this.readTaskAssignment(resource,_OBS_46(resource.node,_OBS_1[104]));
	},readResourceNextAssignment: function(resource,assignment)
	{
		if(!assignment.node){return null;}
		return this.readTaskAssignment(resource,_OBS_50(assignment.node.nextSibling,_OBS_1[157]))
	},insertNode: function(node,parent,previousSibling,gName)
	{
		if(parent)
		{
			var parentNode=_OBS_46(parent.node,gName);
			if(!parentNode)
			{
				parentNode=parent.node.ownerDocument.createElement(gName);
				parent.node.appendChild(parentNode);
			}
			if(previousSibling)
			{
				if(previousSibling.node.nextSibling)
				{
					parentNode.insertBefore(node,previousSibling.node.nextSibling);
				}
				else
				{
					parentNode.appendChild(node);
				}
			}
			else
			{
				parentNode.insertBefore(node,parentNode.firstChild);
			}
		}
		else
		{
			this.doc.documentElement.appendChild(node);
		}
	},addTask: function(parent,pTask)
	{
		var task=new SFDataTask();
		if(this.saveChange)
		{
			var node=parent.node.ownerDocument.createElement(_OBS_1[160]);
			this.insertNode(node,parent,pTask,_OBS_1[109]);
			task.node=node;
		}
		return task;
	},deleteTask: function(task)
	{
		if(!this.saveChange){return;}
		task.node.parentNode.removeChild(task.node);
	},moveTask: function(task,parentTask,pTask)
	{
		if(!this.saveChange){return;}
		task.node.parentNode.removeChild(task.node);
		this.insertNode(task.node,parentTask,pTask,_OBS_1[109]);
	},addResource: function(parent,pResource)
	{
		var resource=new SFDataResource();
		if(this.saveChange)
		{
			var node=parent.node.ownerDocument.createElement(_OBS_1[159]);
			this.insertNode(node,parent,pResource,_OBS_1[107]);
			resource.node=node;
		}
		return resource;
	},deleteResource: function(resource)
	{
		if(!this.saveChange){return;}
		resource.node.parentNode.removeChild(resource.node);
	},moveResource: function(resource,parentResource,pResource)
	{
		if(!this.saveChange){return;}
		resource.node.parentNode.removeChild(resource.node);
		this.insertNode(resource.node,parentResource,pResource,_OBS_1[107]);
	},addLink: function(selfTask,preTask,type)
	{
		var link=new SFDataLink();
		if(this.saveChange)
		{
			var doc=selfTask.node.ownerDocument;
			var node=doc.createElement(_OBS_1[115]);
			var child=doc.createElement(_OBS_1[122]);
			_OBS_48(child,preTask.UID);
			node.appendChild(child);
			var child=doc.createElement(_OBS_1[120]);
			_OBS_48(child,type);
			node.appendChild(child);
			link.node=node;
			link.setProperty(_OBS_1[120],type);
			var linksNode=_OBS_46(selfTask.node,"Links");
			if(!linksNode)
			{
				linksNode=selfTask.node.ownerDocument.createElement("Links");
				selfTask.node.appendChild(linksNode);
			}
			linksNode.appendChild(node);
		}
		return link;
	},deleteLink: function(link)
	{
		if(!this.saveChange){return;}
		link.node.parentNode.removeChild(link.node);
	},addAssignment: function(task,resource,units)
	{
		var assignment=new SFDataAssignment();
		if(this.saveChange)
		{
			var doc=this.doc;
			var node=doc.createElement(_OBS_1[157]);
			var child=doc.createElement(_OBS_1[119]);
			_OBS_48(child,task.UID);
			node.appendChild(child);
			var child=doc.createElement(_OBS_1[118]);
			_OBS_48(child,resource.UID);
			node.appendChild(child);
			var child=doc.createElement(_OBS_1[138]);
			_OBS_48(child,units);
			node.appendChild(child);
			assignment.node=node;
			var assignmentsNode=_OBS_46(task.node,_OBS_1[103]);
			if(!assignmentsNode)
			{
				assignmentsNode=task.node.ownerDocument.createElement(_OBS_1[103]);
				task.node.appendChild(assignmentsNode);
			}
			assignmentsNode.appendChild(node);
		}
		return assignment;
	},deleteAssignment: function(assignment)
	{
		if(!this.saveChange){return;}
		assignment.node.parentNode.removeChild(assignment.node);
	}});
	 function SFDataProject(doc,config)
	{
		_OBS_4(this,{taskReader:{},taskWriter:{},resourceReader:{},resourceWriter:{},linkReader:{},linkWriter:{},assignmentReader:{},assignmentWriter:{}});
		config=config?config:new SFConfig();
		_OBS_53(this,config.getConfigObj("SFDataProject"));
		_OBS_4(this,{doc:doc,config:config});
		
		this.addDefaultProperty();
	}
	SFDataProject.prototype=new SFDataXmlBase()
	_OBS_4(SFDataProject.prototype,{initialize: function()
	{
		SFDataXmlBase.prototype.initialize.apply(this,arguments);
	},loadXml: function(doc)
	{
		if(doc){this.doc=doc;}
		doc=this.doc;
		if(!doc)
		{
			this.doc=doc=_OBS_45();
		}
		if(!doc.documentElement)
		{
			doc.appendChild(doc.createElement("Project"));
		}
		var node=this.doc.documentElement,child=node.firstChild;
		while(child)
		{
			switch(child.nodeName)
			{
				case _OBS_1[109]:
					this.tasksNode=child;
					break;
				case _OBS_1[107]:
					this.resourcesNode=child;
					break;
				case _OBS_1[103]:
					this.assignmentsNode=child;
					break;
				case "ExtendedAttributes":
					this.addExtendedAttributes(child);
					break;
			}
			child=child.nextSibling
		}
		this.loaded=true;
	},getCalendar: function()
	{
		var calId;
		var node=_OBS_46(this.doc.documentElement,_OBS_1[111]);
		if(node)
		{
			calId=_OBS_47(node);
			var calsNode=_OBS_46(this.doc.documentElement,"Calendars");
			for(var child=calsNode.firstChild;child;child=child.nextSibling)
			{
				if(child.nodeName!="Calendar"){continue;}
				if(_OBS_47(_OBS_46(child,"UID"))==calId)
				{
					return this.readCalendar(child); 
				}
			}
		}
		return _OBS_56(_OBS_1[173]);
	},getTasksNode: function()
	{
		if(!this.loaded){this.loadXml()}
		if(!this.tasksNode)
		{
			this.tasksNode=this.doc.createElement(_OBS_1[109]);
			this.doc.documentElement.appendChild(this.tasksNode);
		}
		return this.tasksNode;
	},getResourcesNode: function()
	{
		if(!this.loaded){this.loadXml()}
		if(!this.resourcesNode)
		{
			this.resourcesNode=this.doc.createElement(_OBS_1[107]);
			this.doc.documentElement.appendChild(this.resourcesNode);
		}
		return this.resourcesNode;
	},getAssignmentsNode: function()
	{
		if(!this.loaded){this.loadXml()}
		if(!this.assignmentsNode)
		{
			this.assignmentsNode=this.doc.createElement(_OBS_1[103]);
			this.doc.documentElement.appendChild(this.assignmentsNode);
		}
		return this.assignmentsNode;
	},readRootTask: function()
	{
		var rootTaskNode=_OBS_50(this.getTasksNode().firstChild,_OBS_1[160]);
		if(!rootTaskNode)
		{
			var task=this.addTask();
			return task;
		}
		return this.readTask(rootTaskNode);
	},readTaskFirstChild: function(task)
	{
	    if(!task.node){return null;}
		var selfLevel=task.OriginalLevel;
		var node=_OBS_50(task.node.nextSibling,_OBS_1[160]);
		if(node)
		{
			var level=_OBS_46(node,_OBS_1[136])?_OBS_47(_OBS_46(node,_OBS_1[136])):_OBS_47(_OBS_46(node,_OBS_1[137])).split(".").length;
			if(level>selfLevel)
			{
				return this.readTask(node);
			}
		}
		return null;
	},readTaskNextSibling: function(task)
	{
	     if(!task.node){return null;}
		var selfLevel=task.OriginalLevel;
		
		for(var node=task.node.nextSibling;node;node=node.nextSibling)
		{
			if(node.nodeName!=_OBS_1[160]){continue;}
			
			var level=_OBS_46(node,_OBS_1[136])?_OBS_47(_OBS_46(node,_OBS_1[136])):_OBS_47(_OBS_46(node,_OBS_1[137])).split(".").length;
			if(level>selfLevel){continue;}
			if(level==selfLevel)
			{
				return this.readTask(node);
			}
			break;
		}
		return null;
	},readRootResource: function()
	{
		var rootResourceNode=this.getResourcesNode().firstChild;
		if(!rootResourceNode)
		{
			var resource=this.addResource('0');
			return resource;
		}
		return this.readResource(rootResourceNode);
	},readResourceFirstChild: function(resource)
	{
	     if(!resource.node){return null;}
		if(resource.node!=_OBS_50(this.getResourcesNode().firstChild,_OBS_1[159])){return null;}
		return this.readResource(_OBS_50(resource.node.nextSibling,_OBS_1[159]));
	},readResourceNextSibling: function(resource)
	{
	    if(!resource.node){return null;}
		if(resource.node==_OBS_50(this.getResourcesNode().firstChild,_OBS_1[159])){return null;}
		return this.readResource(_OBS_50(resource.node.nextSibling,_OBS_1[159]));
	},readTaskFirstLink: function(task)
	{
		var node,taskNode=task.node;
		if(!taskNode){return null;}
		for(node=taskNode.firstChild;node;node=node.nextSibling){if(node.nodeName==_OBS_1[115] || node.nodeName==_OBS_1[106]){break;}}
		if(node==null){node=_OBS_46(taskNode,"Links/*");}
		return this.readTaskLink(task,node);
	},readTaskNextLink: function(task,link)
	{
		var node,linkNode=link.node;
		if(!linkNode){return null;}
		for(node=linkNode.nextSibling;node;node=node.nextSibling){if(node.nodeName==_OBS_1[115] || node.nodeName==_OBS_1[106]){break;}}
		if(!node && linkNode.parentNode.nodeName!="Links")
		{
			node=_OBS_46(linkNode,_OBS_1[105]);
		}
		return this.readTaskLink(task,node);
	},readTaskFirstAssignment: function(task)
	{
		var uid=task.UID;
		for(var node=this.getAssignmentsNode().firstChild;node;node=node.nextSibling)
		{
			if(node.nodeName!=_OBS_1[157]){continue;}
			if(_OBS_47(_OBS_46(node,_OBS_1[119]))==uid)
			{
				return this.readTaskAssignment(task,node);
			}
		}
		return null;
	},readTaskNextAssignment: function(task,assignment)
	{
	    if(!assignment.node){return null;}
		var uid=task.UID;
		for(var node=assignment.node.nextSibling;node;node=node.nextSibling)
		{
			if(node.nodeName!=_OBS_1[157]){continue;}
			if(_OBS_47(_OBS_46(node,_OBS_1[119]))==uid)
			{
				return this.readTaskAssignment(task,node);
			}
		}
		return null;
	},readResourceFirstAssignment: function(resource)
	{
		var uid=resource.UID;
		for(var node=this.getAssignmentsNode().firstChild;node;node=node.nextSibling)
		{
			if(node.nodeName!=_OBS_1[157]){continue;}
			if(_OBS_47(_OBS_46(node,_OBS_1[118]))==uid)
			{
				return this.readResourceAssignment(resource,node);
			}
		}
		return null;
	},readResourceNextAssignment: function(resource,assignment)
	{
	    if(!assignment.node){return null;}
		var uid=resource.UID;
		for(var node=assignment.node.nextSibling;node;node=node.nextSibling)
		{
			if(node.nodeName!=_OBS_1[157]){continue;}
			if(_OBS_47(_OBS_46(node,_OBS_1[118]))==uid)
			{
				return this.readResourceAssignment(resource,node);
			}
		}
		return null;
	},addTask: function(parent,pTask)
	{
		var task=new SFDataTask();
		if(this.saveChange)
		{
			var tasksNode=this.getTasksNode();
			var node=tasksNode.ownerDocument.createElement(_OBS_1[160]);
			if(parent)
			{
				var beforeNode=pTask?pTask.node:parent.node;
				if(beforeNode.nextSibling)
				{
					tasksNode.insertBefore(node,beforeNode.nextSibling);
				}
				else
				{
					tasksNode.appendChild(node);
				}
			}
			else
			{
				tasksNode.insertBefore(node,tasksNode.firstChild);
			}
			task.node=node;
		}
		return task;
	},deleteTask: function(task)
	{
		if(!this.saveChange){return;}
		task.node.parentNode.removeChild(task.node);
	},addResource: function(parent,pResource)
	{
		var resource=new SFDataResource();
		if(this.saveChange)
		{
			var resourcesNode=this.getResourcesNode();
			var node=resourcesNode.ownerDocument.createElement(_OBS_1[159]);
			var beforeNode=pResource?pResource.node:parent.node;
			if(beforeNode.nextSibling)
			{
				resourcesNode.insertBefore(node,beforeNode.nextSibling);
			}
			else
			{
				resourcesNode.appendChild(node);
			}
			resource.node=node;
		}
		return resource;
	},deleteResource: function(resource)
	{
		if(!this.saveChange){return;}
		resource.node.parentNode.removeChild(resource.node);
	},addLink: function(selfTask,preTask,type)
	{
		var link=new SFDataLink();
		if(this.saveChange)
		{
			var doc=selfTask.node.ownerDocument;
			var node=doc.createElement(_OBS_1[115]);
			var child=doc.createElement(_OBS_1[122]);
			_OBS_48(child,preTask.UID);
			node.appendChild(child);
			link.node=node;
			if(type)
			{
				var child=doc.createElement(_OBS_1[120]);
				_OBS_48(child,type);
				node.appendChild(child);
				link.setProperty(_OBS_1[120],type);
			}
			selfTask.node.appendChild(node);
		}
		return link;
	},deleteLink: function(link)
	{
		if(!this.saveChange){return;}
		link.node.parentNode.removeChild(link.node);
	},addAssignment: function(task,resource,units)
	{
		var assignment=new SFDataAssignment();
		if(this.saveChange)
		{
			var doc=this.doc;
			var node=doc.createElement(_OBS_1[157]);
			var child=doc.createElement(_OBS_1[119]);
			_OBS_48(child,task.UID);
			node.appendChild(child);
			var child=doc.createElement(_OBS_1[118]);
			_OBS_48(child,resource.UID);
			node.appendChild(child);
			if(units)
			{
				var child=doc.createElement(_OBS_1[138]);
				_OBS_48(child,units);
				node.appendChild(child);
				assignment.setProperty(_OBS_1[138],units);
			}
			assignment.node=node;
			this.getAssignmentsNode().appendChild(node);
		}
		return assignment;
	},deleteAssignment: function(assignment)
	{
		if(!this.saveChange){return;}
		assignment.node.parentNode.removeChild(assignment.node);
	}});
	 function SFDataComponent(){}
	_OBS_4(SFDataComponent.prototype,{initialize: function(){},remove: function(){},depose: function(){this.remove();}});
	 function SFDataCalculateTimeComponent(){}
	SFDataCalculateTimeComponent.prototype=new SFDataComponent()
	_OBS_4(SFDataCalculateTimeComponent.prototype,{initialize: function(data)
	{
		
		if(!data.autoCalculateTime){return false;}
		this.listeners=[
			_OBS_28(data,_OBS_1[102],this,this.onTaskChange),
			_OBS_28(data,_OBS_1[101],this,this.onTaskDelete),
			_OBS_28(data,_OBS_1[100],this,this.onTaskMove)
		];
		return true;
	},onTaskChange: function(task,name,value)
	{
		if(name!=_OBS_1[139] && name!=_OBS_1[140]){return;}
		if(task.getParentTask()){task.getParentTask().checkTime();}
	},onTaskDelete: function(task,pTask)
	{
		if(pTask){pTask.checkTime();}
	},onTaskMove: function(task,pTask)
	{
		if(pTask){pTask.checkTime();}
		if(task.getParentTask()){task.getParentTask().checkTime();}
	}});
	 function SFDataOutlineComponent(){}
	SFDataOutlineComponent.prototype=new SFDataComponent()
	_OBS_4(SFDataOutlineComponent.prototype,{initialize: function(data)
	{
		this.listeners=[];
		var modules=data.getModules();
		for(var i=modules.length-1;i>=0;i--)
		{
			if(!data[_OBS_1[147]+modules[i]]){continue;}
			var module=modules[i].toLowerCase();
			this.listeners=this.listeners.concat(
				[
					_OBS_28(data,module+_OBS_1[151],this,this.onElementRegister),
					_OBS_28(data,_OBS_1[143]+module+"add",this,this.onElementAdd),
					_OBS_28(data,_OBS_1[143]+module+_OBS_1[148],this,this.onElementDelete),
					_OBS_28(data,_OBS_1[143]+module+_OBS_1[144],this,this.onElementMove)
				]
			);
		}
	},setOutline: function(element,toChild)
	{
		var parent=element.getParent(),number='0',level=0;
		if(parent)
		{
			number=(parent.OutlineLevel==0)?""+element.getSiblingIndex():parent.OutlineNumber+"."+element.getSiblingIndex();
			level=parent.OutlineLevel+1;
		}
		var changed=(number!=element[_OBS_1[137]]);
		element.setProperty(_OBS_1[137],number);
		element.setProperty(_OBS_1[136],level);
		if(toChild && changed && element.Summary)
		{
			for(var child=element.getFirstChild();child;child=child.getNextSibling())
			{
				this.setOutline(child,true);
			}
		}
	},onElementRegister: function(element)
	{
		this.setOutline(element,false);
	},onElementAdd: function(element)
	{
		for(var t=element;t;t=t.getNextSibling())
		{
			this.setOutline(t,true);
		}
	},onElementDelete: function(element,parent,pt)
	{
		if(!parent){return;}
		for(var t=pt?pt.getNextSibling():parent.getFirstChild();t;t=t.getNextSibling())
		{
			this.setOutline(t,true);
		}
	},onElementMove: function(element,parentElement,previousSibling)
	{
		if(parentElement)
		{
			for(var t=previousSibling?previousSibling.getNextSibling():parentElement.getFirstChild();t;t=t.getNextSibling())
			{
				this.setOutline(t,true);
			}
		}
		for(var t=element;t;t=t.getNextSibling())
		{
			this.setOutline(t,true);
		}
	}});
	 function SFDataIDComponent(){}
	SFDataIDComponent.prototype=new SFDataComponent()
	_OBS_4(SFDataIDComponent.prototype,{initialize: function(data)
	{
		this.listeners=[];
		var modules=data.getModules();
		for(var i=modules.length-1;i>=0;i--)
		{
			if(!data[_OBS_1[147]+modules[i]]){continue;}
			var module=modules[i].toLowerCase();
			this.listeners=this.listeners.concat(
				[
					_OBS_28(data,module+_OBS_1[151],this,this.onElementRegister),
					_OBS_28(data,_OBS_1[143]+module+"add",this,this.onElementAdd),
					_OBS_28(data,_OBS_1[143]+module+_OBS_1[148],this,this.onElementDelete),
					_OBS_28(data,_OBS_1[143]+module+_OBS_1[144],this,this.onElementMove)
				]
			);
		}
	},setID: function(element)
	{
		var id=element.getParent()?element.getPrevious().ID+1:0;
		if(!isNaN(id) && id!=element.ID)
		{
			element.setProperty('ID',id);
			return true;
		}
		return false;
	},onElementRegister: function(element)
	{
		this.setID(element);
	},onElementAdd: function(element)
	{
		for(var t=element.getNext();t;t=t.getNext())
		{
			if(!this.setID(t)){break;}
		}
	},onElementDelete: function(element,parent,pt)
	{
		if(!parent){return;}
		for(var t=pt?pt.getNext():parent.getNext();t;t=t.getNext())
		{
			if(!this.setID(t)){break;}
		}
	},onElementMove: function(element,parentElement,previousSibling)
	{
		var ele,elements=[element];
		if(parentElement){elements.push(previousSibling?previousSibling.getNext():parentElement.getNext());}
		elements.sort(function(a,b)
		{
			if(!a || !b){return 0;}
			return a.data.compareElement(a,b);
		});
		while(elements.length>0)
		{
			for(var t=elements.pop();t;t=t.getNext())
			{
				if(!this.setID(t)){break;}
			}
		}
	}});
	 function SFDataReadOnlyComponent(){}
	SFDataReadOnlyComponent.prototype=new SFDataComponent()
	_OBS_4(SFDataReadOnlyComponent.prototype,{initialize: function(data)
	{
		if(data.ignoreReadOnly){return false;}
		this.listeners=[]
		var modules=data.getModules();
		this.ignoreFields={};
		for(var i=modules.length-1;i>=0;i--)
		{
			var module=modules[i].toLowerCase();
			var ps=data[module+"ReadonlyIgnoreProperty"];
			this.ignoreFields[module]=ps?ps.split(","):[];
			this.listeners=this.listeners.concat(
				[
					_OBS_28(data,_OBS_1[149]+module+_OBS_1[141],this,this.onElementChange),
					_OBS_28(data,_OBS_1[149]+module+_OBS_1[148],this,this.onElementAction),
					_OBS_28(data,_OBS_1[149]+module+_OBS_1[144],this,this.onElementAction)
				]
			);
		}
		return true;
	},onElementChange: function(returnObj,element,name)
	{
		if(_OBS_13(this.ignoreFields[element.elementType.toLowerCase()],name)){return;}
		if(element[_OBS_1[135]]){returnObj.returnValue=false;}
	},onElementAction: function(returnObj,element)
	{
		if(element[_OBS_1[135]]){returnObj.returnValue=false;}
	}});
	 function SFDataLogging(data)
	{
		this.setTaskFields("Name,Start,Finish,Summary,PercentComplete,Notes")
		this.setLinkFields(_OBS_1[120]);
		this.clear();
		if(data){data.addComponent(this);}
	}
	SFDataLogging.prototype=new SFDataComponent()
	_OBS_4(SFDataLogging.prototype,{initialize: function(data)
	{
		this.start(data);
	},start: function(data)
	{
		this.stop();
		this.listeners=[
			_OBS_28(data,_OBS_1[99],this,this.onTaskAdd),
			_OBS_28(data,_OBS_1[101],this,this.onTaskDelete),
			_OBS_28(data,_OBS_1[100],this,this.onTaskMove),
			_OBS_28(data,_OBS_1[102],this,this.onTaskChange),

			_OBS_28(data,_OBS_1[155],this,this.onLinkAdd),
			_OBS_28(data,"afterlinkdelete",this,this.onLinkDelete),
			_OBS_28(data,_OBS_1[98],this,this.onLinkChange)
			];
	},clear: function()
	{
		_OBS_4(this,{
			newTasks:[],updateTasks:[],moveTasks:[],deleteTasks:[],
			newLinks:[],updateLinks:[],deleteLinks:[]
		});
	},getXml: function()
	{
		var doc=_OBS_45();
		var root=doc.createElement("Log");
		doc.appendChild(root);
		
		var elements=this.newTasks;
		if(elements && elements.length>0)
		{
			var groupNode=this.addNode(root,"AddTasks");
			for(var i=0;i<elements.length;i++)
			{
				var element=elements[i];
				if(!element.task.data){continue;}
				var elementNode=this.addNode(groupNode,_OBS_1[160]);
				this.addPropertyNode(elementNode,element.task,["UID"]);
				this.addPropertyNode(elementNode,element.task,element.fields);
				if(element.task.getParentTask()){this.addNode(elementNode,"ParentUID",element.task.getParentTask().UID);}
				if(element.task.getPreviousSibling()){this.addNode(elementNode,_OBS_1[97],element.task.getPreviousSibling().UID);}
			}
		}
		var elements=this.updateTasks;
		if(elements && elements.length>0)
		{
			var groupNode=this.addNode(root,"UpdateTasks");
			for(var i=0;i<elements.length;i++)
			{
				var element=elements[i];
				if(!element.task.data){continue;}
				var elementNode=this.addNode(groupNode,_OBS_1[160]);
				this.addPropertyNode(elementNode,element.task,["UID"]);
				this.addPropertyNode(elementNode,element.task,element.fields);
			}
		}
		var elements=this.moveTasks;
		if(elements && elements.length>0)
		{
			var groupNode=this.addNode(root,"MoveTasks");
			for(var i=0;i<elements.length;i++)
			{
				var element=elements[i];
				if(!element.task.data){continue;}
				var elementNode=this.addNode(groupNode,_OBS_1[160]);
				this.addPropertyNode(elementNode,element.task,["UID"]);
				if(element.task.getParentTask()){this.addNode(elementNode,"ParentUID",element.task.getParentTask().UID);}
				if(element.task.getPreviousSibling()){this.addNode(elementNode,_OBS_1[97],element.task.getPreviousSibling().UID);}
			}
		}
		var elements=this.deleteTasks;
		if(elements && elements.length>0)
		{
			var groupNode=this.addNode(root,"DeleteTasks");
			for(var i=0;i<elements.length;i++)
			{
				var element=elements[i];
				var elementNode=this.addNode(groupNode,_OBS_1[160]);
				this.addPropertyNode(elementNode,element.task,["UID"]);
			}
		}
		
		var elements=this.newLinks;
		
		if(elements && elements.length>0)
		{
			var groupNode=this.addNode(root,"AddLinks");
			for(var i=0;i<elements.length;i++)
			{
				var element=elements[i];
				if(!element.link.data){continue;}
				var elementNode=this.addNode(groupNode,_OBS_1[158]);
				this.addPropertyNode(elementNode,element.link,["UID",_OBS_1[120]]);
				
				this.addPropertyNode(elementNode,element.link,element.fields);
				if(element.link.getPredecessorTask()){this.addNode(elementNode,_OBS_1[122],element.link.getPredecessorTask().UID);}
				if(element.link.getSuccessorTask()){this.addNode(elementNode,_OBS_1[121],element.link.getSuccessorTask().UID);}
			}
		}
		var elements=this.updateLinks;
		if(elements && elements.length>0)
		{
			var groupNode=this.addNode(root,"UpdateLinks");
			for(var i=0;i<elements.length;i++)
			{
				var element=elements[i];
				if(!element.link.data){continue;}
				var elementNode=this.addNode(groupNode,_OBS_1[158]);
				this.addPropertyNode(elementNode,element.link,["UID"]);
				if(element.link.getPredecessorTask()){this.addNode(elementNode,_OBS_1[122],element.link.getPredecessorTask().UID);}
				if(element.link.getSuccessorTask()){this.addNode(elementNode,_OBS_1[121],element.link.getSuccessorTask().UID);}
				this.addPropertyNode(elementNode,element.link,element.fields);
			}
		}
		var elements=this.deleteLinks;
		if(elements && elements.length>0)
		{
			var groupNode=this.addNode(root,"DeleteLinks");
			for(var i=0;i<elements.length;i++)
			{
				var element=elements[i];
				var elementNode=this.addNode(groupNode,_OBS_1[158]);
				this.addPropertyNode(elementNode,element.link,["UID"]);
			}
		}

		return doc;
	},setTaskFields: function(fields)
	{
		this.taskFields=typeof(fields)==_OBS_1[181]?fields.split(","):fields;
	},onTaskAdd: function(task)
	{
		var obj=_OBS_13(this.deleteTasks,task,function(a,b){return a.task==b});
		if(obj){_OBS_14(this.deleteTasks,obj);return;}
		obj=_OBS_13(this.moveTasks,task,function(a,b){return a.task==b});
		if(obj){_OBS_14(this.moveTasks,obj);}
		var fields=[];
		obj=_OBS_13(this.updateTasks,task,function(a,b){return a.task==b});
		if(obj){_OBS_14(this.updateTasks,obj);fields=obj.fields;}
		this.newTasks.push({task:task,fields:fields});
	},onTaskDelete: function(task)
	{
		var obj=_OBS_13(this.newTasks,task,function(a,b){return a.task==b});
		if(obj){_OBS_14(this.newTasks,obj);return;}
		obj=_OBS_13(this.moveTasks,task,function(a,b){return a.task==b});
		if(obj){_OBS_14(this.moveTasks,obj);}
		obj=_OBS_13(this.updateTasks,task,function(a,b){return a.task==b});
		if(obj){_OBS_14(this.updateTasks,obj);}
		this.deleteTasks.push({task:task});
	},onTaskMove: function(task,pTask,preTask)
	{
		if(_OBS_13(this.deleteTasks,task,function(a,b){return a.task==b})){return;}
		if(_OBS_13(this.newTasks,task,function(a,b){return a.task==b})){return;}
		if(_OBS_13(this.moveTasks,task,function(a,b){return a.task==b})){return;}
		this.moveTasks.push({task:task});
	},onTaskChange: function(task,name,value)
	{
		if(_OBS_13(this.deleteTasks,task,function(a,b){return a.task==b})){return;}
		if(!_OBS_13(this.taskFields,name)){return;}
		var obj=_OBS_13(this.newTasks,task,function(a,b){return a.task==b});
		if(!obj){obj=_OBS_13(this.updateTasks,task,function(a,b){return a.task==b});}
		if(!obj){this.updateTasks.push(obj={task:task,fields:[]});}
		if(_OBS_13(obj.fields,name)){return;}
		obj.fields.push(name);
	},setLinkFields: function(fields)
	{
		this.linkFields=typeof(fields)==_OBS_1[181]?fields.split(","):fields;
	},onLinkAdd: function(link)
	{
		var obj=_OBS_13(this.deleteLinks,link,function(a,b){return a.link==b});
		if(obj){_OBS_14(this.deleteLinks,obj);return;}
		var fields=[];
		obj=_OBS_13(this.updateLinks,link,function(a,b){return a.link==b});
		if(obj){_OBS_14(this.updateLinks,obj);fields=obj.fields;}
		this.newLinks.push({link:link,fields:fields});
		
	},onLinkDelete: function(link)
	{
		var obj=_OBS_13(this.newLinks,link,function(a,b){return a.link==b});
		if(obj){_OBS_14(this.newLinks,obj);return;}
		obj=_OBS_13(this.updateLinks,link,function(a,b){return a.link==b});
		if(obj){_OBS_14(this.updateLinks,obj);}
		this.deleteLinks.push({link:link});
	},onLinkChange: function(link,name,value)
	{
		if(_OBS_13(this.deleteLinks,link,function(a,b){return a.link==b})){return;}
		if(!_OBS_13(this.linkFields,name)){return;}
		var obj=_OBS_13(this.newLinks,link,function(a,b){return a.link==b});
		if(!obj){obj=_OBS_13(this.updateLinks,link,function(a,b){return a.link==b});}
		if(!obj){this.updateLinks.push(obj={link:link,fields:[]});}
		if(_OBS_13(obj.fields,name)){return;}
		obj.fields.push(name);
	},addNode: function(parentNode,name,value)
	{
		var child=parentNode.ownerDocument.createElement(name);
		if(value!=null)
		{
			child.appendChild(parentNode.ownerDocument.createTextNode(this.pack(value)));
		}
		parentNode.appendChild(child);
		return child;
	},addPropertyNode: function(node,element,property)
	{
		property=property?property:["UID"]
		for(var i=property.length-1;i>=0;i--)
		{
			this.addNode(node,property[i],element[property[i]]);
		}
	},stop: function()
	{
		if(!this.listeners){return;}
		var listener;
		while(listener=this.listeners.pop()){_OBS_32(listener);}
	},pack: function(value)
	{
		switch(typeof(value))
		{
			case "boolean":
				return value?'1':'0';
			case _OBS_1[184]:
				if(value.constructor==Date)
				{
					return _OBS_9(value,'s');
				}
				break;
		}
		return value.toString();
	},depose: function()
	{
		this.stop();
		this.clear();
		for(var key in this){this[key]=null;}
	}});
	 function SFGantt(gConfig,data)
	{
		gConfig=this.config=gConfig?gConfig:new SFConfig();
		this.elementType=_OBS_1[160];
		_OBS_53(this,gConfig.getConfigObj("SFGantt"));
		this.initContainer();
		this.setViewSize(_OBS_16(this.container));
		this.controls=[];
		var doc=this.container.ownerDocument;
		if(doc.createDocumentFragment){this.containerFragment=doc.createDocumentFragment();}
		var elementList;
		this.addControl(new SFGanttTooltipControl());
		this.addControl(new SFGanttContextMenuControl());
		this.addControl(new SFGanttLayoutControl());
		this.addControl(new SFGanttBodyHeightControl());
		
		
		
		
		this.addControl(new SFGanttImageControl());
		this.addControl(new SFGanttFieldList(this[this.elementType.toLowerCase()+_OBS_1[96]].split(",")));
		this.addControl(new SFGanttCursorControl());
		this.addControl(new SFGanttDrawControl());
		this.addControl(new SFGanttViewItemsControl(this.elementType));
		this.addControl(new SFGanttElementSelectControl({elementType:this.elementType}));
		this.addControl(new SFGanttScrollControl());
		this.addControl(new SFGanttChangeEventControl({elementType:this.elementType}));
		
		
		
		
		this.addControl(elementList=new SFGanttElementList({fieldNames:this[this.elementType.toLowerCase()+_OBS_1[96]].split(","),bgColor:this.bodyBgColor,elementType:this.elementType}));
		this.addControl(new SFGanttElementList({fieldNames:this[this.elementType.toLowerCase()+"IdFieldNames"].split(","),bgColor:this.idCellBgColor,mainList:elementList,elementType:this.elementType}));
		this.addControl(new SFGanttCollapseControl());
		this.addControl(new SFGanttDragResizeControl());
		this.addControl(new SFGanttHelpLinkControl());
		this.addControl(new SFGanttLogoControl());
		this.addControl(new SFGanttDialogControl());
		this.addControl(new SFGanttPrintControl());
		this.addControl(new SFGanttSizeLimitControl());
		this.addControl(new SFGanttTimeControl());
		this.addControl(new SFGanttMapPanel());
		this.addControl(new SFGanttTimePanel());
		this.addControl(new SFGanttZoomControl());
		this.addControl(new SFGanttAutoResizeControl());
		this.addControl(new SFGanttTimeScroller());
		this.addControl(new SFGanttDivScroller());
		switch(this.elementType)
		{
			case _OBS_1[160]:
				this.addControl(new SFGanttSelectTaskOperateControl());
				this.addControl(new SFGanttTasksMap());
				this.addControl(new SFGanttLinksMap());
				break;
			case _OBS_1[159]:
				this.addControl(new SFGanttResourceMap());
		}
		this.addControl(new SFGanttTimeScrollNotice());
		this.addControl(new SFGanttListScrollNotice());
		this.addControl(new SFGanttCalendarControl());
		this.addControl(new SFGanttCalDiv());
		this.addControl(new SFGanttDragZoomControl());
		this.addControl(new SFGanttTimeSegmentation());
		this.addControl(new SFGanttWorkingMask());
		this.addControl(new SFGanttTimeLine());
		this.addControl(new SFGanttDefaultMenuControl());
		if(this.containerFragment)
		{
		    this.container.appendChild(this.containerFragment);
		    this.containerFragment=null;
		}
		if(data){this.setData(data);}
	}
	_OBS_4(SFGantt.prototype,{initContainer: function()
	{
		var container=this.container;
		this.container=container=(typeof(container)==_OBS_1[184])?container:document.getElementById(container);
		var child,doc=this.container.ownerDocument;
		
		try{doc.execCommand("BackgroundImageCache", false, true);}catch(e){}
		
		var style=this.container.style;
		if(style.position!=_OBS_1[179]){style.position=_OBS_1[178];}
		_OBS_4(style,{padding:'0px',margin:'0px',textAlign:_OBS_1[95],overflow:_OBS_1[180],backgroundColor:this.bodyBgColor,fontSize:this.fontSize+"px"});
		while(child=container.firstChild){container.removeChild(child)}
	},addControl: function(control)
	{
		if(!control){return;}
		control.added=true;
		if(!control.initialize(this,this.containerFragment?this.containerFragment:this.container)){return false;}
		this.controls.push(control);
		return true;
	},removeControl: function(control)
	{
		if(!control){return;}
		control.remove();
		control.added=false;
		_OBS_14(control);
	},initialize: function()
	{
		if(this.loaded || !this.data){return;}
		this.loaded=true;
		
		_OBS_35(this,_OBS_1[94]);
	},getContainer: function()
	{
		return this.container;
	},setViewSize: function(size)
	{
		var viewSize=this.viewSize;
		if(viewSize && viewSize[0]==size[0] && viewSize[1]==size[1]){return;}
		var returnObj={returnValue:true}
		
		_OBS_35(this,_OBS_1[93],[returnObj,size]);
		if(!returnObj.returnValue){return false;}
		this.viewSize=size;
		
		_OBS_35(this,_OBS_1[92],[size]);
		return true;
	},getViewSize: function()
	{
		return this.viewSize;
	},setData: function(data)
	{
		this.data=data;
		_OBS_53(data,this.config.getConfigObj("SFData"));
		if(!this.loaded){this.initialize();}
	},getData: function()
	{
		return this.data;
	},depose: function()
	{
		
		var controls=this.controls;
		for(var i=controls.length-1;i>=0;i--){this.removeControl(controls[i]);}
		_OBS_29(this.container,true);
	}});
	 function SFGanttControl(){}
	_OBS_4(SFGanttControl.prototype,{initialize: function(){return false;},remove: function()
	{
		var listener,listeners=this.listeners;
		if(listeners)
		{
			while(listener=listeners.pop()){_OBS_32(listener);}
		}
		_OBS_29(this.div);
		delete this.listeners;
		delete this.gantt;
	},isUsing: function()
	{
		return !!this.added;
	},depose: function()
	{
		this.remove();
		_OBS_33(this);
		for(var key in this){this[key]=null;}
	}});
	 function SFGanttImageControl()
	{
		this.sprites={
			icon:{imageSize:[128,48],path:'icon/icon_default'},
			symbol:{imageSize:[36,168],path:'symbol/symbol_000000',autoColor:1},
			scroll:{imageSize:[48,86],path:'scroll/scroll'}
		}
		this.images={
			tab_left:{size:[2,23]},
			tab_right:{size:[2,23]},
			tab_bg:{size:[1,23]},
			tab_f_right:{size:[2,23]},
			tab_f_bg:{size:[1,23]},
			tab_bg:{size:[1,23]},
			tab_bg:{size:[1,23]},
			collapse_open:{size:[9,9]},
			collapse_close:{size:[9,9]},
			map_mask:{size:[4,2]},
			scroll_barbg:{sprite:_OBS_1[91],spritePoint:[0,51],spriteSize:[48,17]},
			scroll_barright:{sprite:_OBS_1[91],spritePoint:[28,0],spriteSize:[3,17]},
			scroll_barcenter:{sprite:_OBS_1[91],spritePoint:[20,0],spriteSize:[8,17]},
			scroll_barleft:{sprite:_OBS_1[91],spritePoint:[17,0],spriteSize:[3,17]},
			scroll_barbg1:{sprite:_OBS_1[91],spritePoint:[0,34],spriteSize:[48,17]},
			scroll_barright1:{sprite:_OBS_1[91],spritePoint:[28,17],spriteSize:[3,17]},
			scroll_barcenter1:{sprite:_OBS_1[91],spritePoint:[20,17],spriteSize:[8,17]},
			scroll_barleft1:{sprite:_OBS_1[91],spritePoint:[17,17],spriteSize:[3,17]},
			scroll_left:{sprite:_OBS_1[91],spritePoint:[0,0],spriteSize:[17,17]},
			scroll_right:{sprite:_OBS_1[91],spritePoint:[31,0],spriteSize:[17,17]},
			scroll_left1:{sprite:_OBS_1[91],spritePoint:[0,17],spriteSize:[17,17]},
			scroll_right1:{sprite:_OBS_1[91],spritePoint:[31,17],spriteSize:[17,17]},
			scroll_bg:{sprite:_OBS_1[91],spritePoint:[0,68],spriteSize:[48,18]},
			dragflag_right:{size:[3,21]},
			dragflag_left:{size:[2,21]},
			logo:{size:[36,36]},
			task_head_1:{sprite:_OBS_1[90],spritePoint:[0,0],spriteSize:[11,11]},
			task_head_2:{sprite:_OBS_1[90],spritePoint:[12,0],spriteSize:[11,11]},
			task_head_3:{sprite:_OBS_1[90],spritePoint:[24,0],spriteSize:[11,11]},
			task_head_3_hollow:{sprite:_OBS_1[90],spritePoint:[0,12],spriteSize:[11,11]},
			task_head_4:{sprite:_OBS_1[90],spritePoint:[12,12],spriteSize:[11,11]},
			task_head_5:{sprite:_OBS_1[90],spritePoint:[24,12],spriteSize:[11,11]},
			task_head_6:{sprite:_OBS_1[90],spritePoint:[0,24],spriteSize:[11,11]},
			task_head_7:{sprite:_OBS_1[90],spritePoint:[12,24],spriteSize:[11,11]},
			task_head_8:{sprite:_OBS_1[90],spritePoint:[24,24],spriteSize:[11,11]},
			task_head_9:{sprite:_OBS_1[90],spritePoint:[0,36],spriteSize:[11,11]},
			task_head_10:{sprite:_OBS_1[90],spritePoint:[12,36],spriteSize:[11,11]},
			task_head_11:{sprite:_OBS_1[90],spritePoint:[24,36],spriteSize:[11,11]},
			task_head_12:{sprite:_OBS_1[90],spritePoint:[0,48],spriteSize:[11,11]},
			task_head_13:{sprite:_OBS_1[90],spritePoint:[12,48],spriteSize:[11,11]},
			task_head_14:{sprite:_OBS_1[90],spritePoint:[24,48],spriteSize:[11,11]},
			task_head_15:{sprite:_OBS_1[90],spritePoint:[0,60],spriteSize:[11,11]},
			task_head_16:{sprite:_OBS_1[90],spritePoint:[12,60],spriteSize:[11,11]},
			task_head_17:{sprite:_OBS_1[90],spritePoint:[24,60],spriteSize:[11,11]},
			task_head_18:{sprite:_OBS_1[90],spritePoint:[0,72],spriteSize:[11,11]},
			task_head_19:{sprite:_OBS_1[90],spritePoint:[12,72],spriteSize:[11,11]},
			task_head_19_hollow:{sprite:_OBS_1[90],spritePoint:[24,72],spriteSize:[11,11]},
			task_head_20:{sprite:_OBS_1[90],spritePoint:[0,84],spriteSize:[11,11]},
			arrow_down:{sprite:_OBS_1[90],spritePoint:[13,84],spriteSize:[9,5]},
			arrow_left:{sprite:_OBS_1[90],spritePoint:[24,85],spriteSize:[5,9]},
			arrow_right:{sprite:_OBS_1[90],spritePoint:[30,85],spriteSize:[5,9]},
			arrow_up:{sprite:_OBS_1[90],spritePoint:[13,90],spriteSize:[9,5]},
			grid_1:{sprite:_OBS_1[90],spritePoint:[0,96],spriteSize:[36,36]},
			grid_2:{sprite:_OBS_1[90],spritePoint:[0,132],spriteSize:[36,36]},
			icon_finished:{sprite:_OBS_1[89],spritePoint:[0,16],spriteSize:[16,16]},
			icon_constraint2:{sprite:_OBS_1[89],spritePoint:[0,0],spriteSize:[16,16]},
			icon_constraint3:{sprite:_OBS_1[89],spritePoint:[16,0],spriteSize:[16,16]},
			icon_constraint4:{sprite:_OBS_1[89],spritePoint:[32,0],spriteSize:[16,16]},
			icon_constraint5:{sprite:_OBS_1[89],spritePoint:[48,0],spriteSize:[16,16]},
			icon_constraint6:{sprite:_OBS_1[89],spritePoint:[64,0],spriteSize:[16,16]},
			icon_constraint7:{sprite:_OBS_1[89],spritePoint:[80,0],spriteSize:[16,16]},
			icon_notes:{sprite:_OBS_1[89],spritePoint:[32,16],spriteSize:[16,16]},
			icon_hyperlink:{sprite:_OBS_1[89],spritePoint:[16,16],spriteSize:[16,16]},
			icon_taskstatus:{sprite:_OBS_1[89],spritePoint:[112,16],spriteSize:[16,16]},
			icon_taskinfo:{sprite:_OBS_1[89],spritePoint:[80,16],spriteSize:[16,16]},
			icon_taskgoto:{sprite:_OBS_1[89],spritePoint:[64,16],spriteSize:[16,16]},
			icon_print:{sprite:_OBS_1[89],spritePoint:[48,16],spriteSize:[16,16]},
			icon_zoomin:{sprite:_OBS_1[89],spritePoint:[0,32],spriteSize:[16,16]},
			icon_zoomout:{sprite:_OBS_1[89],spritePoint:[16,32],spriteSize:[16,16]},
			resize:{sprite:_OBS_1[89],spritePoint:[32,32],spriteSize:[16,16]}
		};
	}
	SFGanttImageControl.prototype=new SFGanttControl()
	_OBS_4(SFGanttImageControl.prototype,{initialize: function(gantt)
	{
		if(gantt.disableCursor){return false;}
		this.gantt=gantt;
		gantt.createImage=_OBS_22(this,_OBS_78);
		gantt.setImageSrc=_OBS_22(this,_OBS_79);
		gantt.setBackgroundImage=_OBS_22(this,_OBS_80);
		return true;
	},remove: function()
	{
		var gantt=this.gantt;
		delete gantt.createImage;
		delete gantt.setImageSrc;
		delete gantt.setBackgroundImage;
		delete this.gantt
	}});
	 function _OBS_78(name,config)
	{
		var imgConfig=this.images[name];
		if(!imgConfig){return null;}
		if(imgConfig.sprite)
		{
			var style={},sprite=this.sprites[imgConfig.sprite],path=sprite.path;
			_OBS_4(style,sprite);
			_OBS_4(style,imgConfig);
			_OBS_4(style,config);
			if(sprite.autoColor && config.color)
			{
				path=path.replace("000000",config.color.substring(1).toUpperCase());
			}
			return _OBS_19(this.gantt.imgPath+path+this.gantt.imgType,style);
		}
		return _OBS_19(this.gantt.imgPath+name+this.gantt.imgType,this.images[name],config);
	}
	 function _OBS_79(img,name)
	{
		var imgConfig=this.images[name];
		if(imgConfig)
		{
			if(imgConfig.sprite)
			{
				_OBS_4(img.firstChild.style,{left:-imgConfig.spritePoint[0]+"px",top:-imgConfig.spritePoint[1]+"px"});
				return;
			}
			_OBS_20(img,this.gantt.imgPath+name+this.gantt.imgType);
		}
	}
	 function _OBS_80(div,name,config)
	{
		var imgConfig=this.images[name];
		if(imgConfig)
		{
			if(imgConfig.sprite)
			{
				var style={},sprite=this.sprites[imgConfig.sprite],path=sprite.path;
				_OBS_4(style,sprite);
				_OBS_4(style,imgConfig);
				_OBS_4(style,config);
				if(sprite.autoColor && config && config.color)
				{
					path=path.replace("000000",config.color.substring(1).toUpperCase());
				}
				_OBS_21(div,this.gantt.imgPath+path+this.gantt.imgType,style);
				return;
			}
			_OBS_21(div,this.gantt.imgPath+name+this.gantt.imgType,this.images[name]);
		}
	}
	_OBS_4(SFGanttImageControl,{createImage:_OBS_78,setImageSrc:_OBS_79,setBackgroundImage:_OBS_80});
	 function SFGanttCalendarItem(unit,num,format)
	{
		this.unit=unit;
		this.number=num;
		this.format=format;
	}
	_OBS_4(SFGanttCalendarItem.prototype,{showHead: function(time)
	{
		var config=window._SFGantt_config.SFGlobal;
		return _OBS_9(time,this.format,config);
	},getFloorTime: function(time)
	{
		switch(this.unit)
		{
			case "Minute":
				var flag=time.getMinutes()%this.number;
				return new Date(time.getFullYear(),time.getMonth(),time.getDate(),time.getHours(),time.getMinutes()-flag);
			case "Hour":
				var flag=time.getHours()%this.number;
				return new Date(time.getFullYear(),time.getMonth(),time.getDate(),time.getHours()-flag);
			case "Dat":
				var flag=(time.valueOf()-time.getTimezoneOffset()*60*1000)%(this.number*24*60*60*1000);
				return new Date(time.valueOf()-flag);
			case "Day":
				var flag=time.getDay()%this.number;
				var newTime=new Date(time.valueOf()-flag*24*60*60*1000);
				return new Date(newTime.getFullYear(),newTime.getMonth(),newTime.getDate());
			case "Week":
				var flag=time.getDay();
				var newTime=new Date(time.valueOf()-flag*24*60*60*1000);
				return new Date(newTime.getFullYear(),newTime.getMonth(),newTime.getDate());
			case _OBS_1[88]:
				var flag=time.getMonth()%this.number;
				return new Date(time.getFullYear(),time.getMonth()-flag);
			case "Year":
				var flag=time.getFullYear()%this.number;
				return new Date(time.getFullYear()-flag);
			default:
				return time;
		}
	},getNextTime: function(time)
	{
		switch(this.unit)
		{
			case "Minute":
				return new Date(time.valueOf()+this.number*60*1000);
			case "Hour":
				return new Date(time.valueOf()+this.number*60*60*1000);
			case "Dat":
			case "Day":
				return new Date(time.valueOf()+this.number*24*60*60*1000);
			case "Week":
				return new Date(time.valueOf()+this.number*7*24*60*60*1000);
			case _OBS_1[88]:
				var year=time.getFullYear(),month=time.getMonth()+this.number;
				if(month==12)
				{
					year++;
					month=0;
				}
				return new Date(year,month);
			case "Year":
				var year=time.getFullYear()+this.number;
				var t=new Date(0);
				t.setYear(year);
				return t;
			default:
				return time;
		}
	}});
	 function SFMenuItem(showHandle,runHandle,text,icon,id,index)
	{
		if(!id)
		{
			if(!SFMenuItem.idNum){SFMenuItem.idNum=0}
			id="MenuItem_"+(SFMenuItem.idNum++);
		}
		index=index?index:500;
		this.setIcon(icon);
		_OBS_4(this,{showHandle:showHandle,runHandle:runHandle,text:text,id:id,index:index});
	}
	_OBS_4(SFMenuItem.prototype,{getIndex: function()
	{
		return this.index;
	},setIndex: function(index)
	{
		this.index=parseInt(index);
	},getText: function()
	{
		return this.text;
	},setText: function(text)
	{
		this.text=text;
	},setIcon: function(icon)
	{
		this.icon=icon;
	}});
	 function SFGanttAutoResizeControl()
	{
	}
	SFGanttAutoResizeControl.prototype=new SFGanttControl()
	_OBS_4(SFGanttAutoResizeControl.prototype,{initialize: function(gantt)
	{
		var style=gantt.getContainer().style;
		if(style.width && style.width.indexOf('%')<0 && style.height && style.height.indexOf('%')<0){return false}
		this.gantt=gantt;
		this.listeners=[
			_OBS_28(gantt.getContainer(),_OBS_1[87],this,this.onResize),
			_OBS_28(window,_OBS_1[87],this,this.onResize),
			_OBS_28(window,_OBS_1[144],this,this.onResize),
			_OBS_28(window,"load",this,this.onResize)
		]
		return true;
	},onResize: function()
	{
		if(!this.timeout){this.timeout=window.setInterval(_OBS_22(this,this.onTime),256);}
		this.changed=true;
		this.idleTimes=0;
	},onTime: function()
	{
		if(!this.changed)
		{
			this.idleTimes++;
			if(this.idleTimes>4)
			{
				window.clearInterval(this.timeout);
				delete this.timeout
			}
			return;
		}
		this.changed=false;
		this.resize();
	},resize: function()
	{
		var gantt=this.gantt;
		gantt.setViewSize(_OBS_16(gantt.getContainer()));
		this.timeout=null;
	}});
	 function SFGanttBodyHeightControl(config)
	{
	}
	SFGanttBodyHeightControl.prototype=new SFGanttControl()
	_OBS_4(SFGanttBodyHeightControl.prototype,{initialize: function(gantt,container)
	{
		this.listeners=[
			_OBS_28(this.gantt=gantt,_OBS_1[86],this,this.onChange)
		];
		return true;
	},onChange: function(heightSpan)
	{
		if(!this.timeout){this.timeout=window.setInterval(_OBS_22(this,this.onTime),64);}
		this.changed=true;
		this.idleTimes=0;
		this.bodyHeight=heightSpan[1];
	},onTime: function()
	{
		if(!this.changed)
		{
			this.idleTimes++;
			if(this.idleTimes>16)
			{
				window.clearInterval(this.timeout);
				delete this.timeout
			}
			return;
		}
		this.changed=false;
		this.setBodyHeight();
	},setBodyHeight: function()
	{
		var mapBody=this.gantt.getLayout(_OBS_1[85]);
		if(mapBody){mapBody.style.height=this.bodyHeight+100+"px";}
	},remove: function()
	{
		if(this.timeout){window.clearInterval(this.timeout);}
		SFGanttControl.prototype.remove.apply(this,arguments);
	}});
	 function SFGanttCalDiv()
	{
	}
	SFGanttCalDiv.prototype=new SFGanttControl()
	_OBS_4(SFGanttCalDiv.prototype,{initialize: function(gantt)
	{
		if(!gantt.getLayout || !gantt.getCalList){return false;}
		var container=gantt.getLayout("mapHead"),doc=gantt.container.ownerDocument;
		if(!container){return false;}
		_OBS_53(this,gantt.config.getConfigObj("SFGanttCalDiv"));
		var div=this.div=doc.createElement("div");
		_OBS_40(div);
		_OBS_4(this,{gantt:gantt,div:div,container:container,cals:{}});
		_OBS_4(div.style,{position:_OBS_1[179],padding:'0px',margin:'0px'});
		for(var i=0;i<this.calNum;i++)
		{
			var calDiv=doc.createElement("div");
			_OBS_4(calDiv.style,{position:_OBS_1[179],padding:'0px',margin:'0px',left:'0px'});
			div.appendChild(calDiv);
		}
		container.appendChild(div);
		this.listeners=[
			_OBS_28(gantt,_OBS_1[94],this,this.onResize),
			_OBS_28(gantt,_OBS_1[84],this,this.onResize),
			_OBS_28(gantt,_OBS_1[83],this,this.showCal),
			_OBS_28(gantt,_OBS_1[144],this,this.showCal)
		];
		this.onResize();
		return true;
	},onResize: function()
	{
		var gantt=this.gantt,div=this.div,container=div.parentNode,size=this.size,s=[container.offsetWidth,container.offsetHeight];
		if(!size || size[1]!=s[1])
		{
			var calNum=this.calNum,height=s[1];
			for(var i=0;i<calNum;i++)
			{
				_OBS_4(div.childNodes[i].style,{top:Math.floor(height*i/calNum)+"px",height:Math.floor(height/calNum)+"px"});
			}
		}
		this.size=s;
		this.showCal();
	},showCal: function()
	{
		var gantt=this.gantt,startTime=gantt.getStartTime(),scale=gantt.getScale(),calList=gantt.getCalList();
		if(!startTime || !scale || !calList){return;}
		startTime=startTime.valueOf();
		this.moveTo(scale,startTime);
		var cals=this.gantt.getCalList(),childNodes=this.div.childNodes;
		for(var i=0;i<this.calNum;i++)
		{
			this.showCalItem(scale,startTime,cals[i],childNodes[this.calNum-i-1],i);
		}
	},showCalItem: function(scale,startTime,cal,calDiv,index)
	{
		var drawObj=this.cals[index];
		if(!drawObj || drawObj.cal!=cal)
		{
			this.clearItem(index);
			this.cals[index]=drawObj={start:startTime,cal:cal,scale:scale};
			calDiv.style.left=(startTime-this.drawStart)/scale+"px";
		}
		else if(drawObj.scale!=scale)
		{
			for(var child=calDiv.firstChild;child;child=child.nextSibling)
			{
				_OBS_4(child.style,{
					left:(child.sTime-drawObj.start)/scale+"px",
					width:(child.eTime-child.sTime)/scale+"px"
				});
			}
			calDiv.style.left=(drawObj.start-this.drawStart)/scale+"px";
			drawObj.scale=scale
		}
		var endTime=startTime+this.container.offsetWidth*scale;
		var osTime=calDiv.firstChild?calDiv.firstChild.sTime:Number.MAX_VALUE;
		var oeTime=calDiv.lastChild?calDiv.lastChild.eTime:Number.MIN_VALUE;
		if(startTime>(calDiv.firstChild?calDiv.firstChild.eTime:Number.MAX_VALUE))
		{
			while(calDiv.firstChild && calDiv.firstChild.eTime<startTime)
			{
				_OBS_29(calDiv.firstChild);
			}
			osTime=calDiv.firstChild?calDiv.firstChild.sTime:Number.MAX_VALUE
		}
		if((calDiv.lastChild?calDiv.lastChild.sTime:Number.MIN_VALUE)>endTime)
		{
			while(calDiv.lastChild && calDiv.lastChild.sTime>endTime)
			{
				_OBS_29(calDiv.lastChild);
			}
			oeTime=calDiv.lastChild?calDiv.lastChild.eTime:Number.MIN_VALUE
		}
		if(startTime<osTime)
		{
			this.addTimeSpans(startTime,Math.min(osTime,endTime),drawObj,calDiv,-1);
			osTime=calDiv.firstChild?calDiv.firstChild.sTime:Number.MAX_VALUE;
			oeTime=calDiv.lastChild?calDiv.lastChild.eTime:Number.MIN_VALUE;
		}
		if(oeTime<endTime)
		{
			this.addTimeSpans(Math.max(oeTime,startTime),endTime,drawObj,calDiv,1);
		}
	},addTimeSpans: function(startTime,endTime,drawObj,calDiv,position)
	{
		var cal=drawObj.cal;
		var sTime=parseInt(cal.getFloorTime(new Date(startTime)).valueOf());
		var lastAdd=null;
		while(sTime<endTime)
		{
			var eTime=parseInt(cal.getNextTime(new Date(sTime)).valueOf());
			var div=calDiv.ownerDocument.createElement("div");
			_OBS_4(div,{sTime:sTime,eTime:eTime});
			var height=Math.floor(this.size[1]/this.calNum)-1;
			_OBS_4(div.style,{position:_OBS_1[179],left:(sTime-drawObj.start)/drawObj.scale+"px",top:'0px',width:(eTime-sTime)/drawObj.scale+"px",height:height,fontSize:Math.floor(height*0.8)+"px",padding:'0px',lineHeight:height+"px",borderRight:_OBS_1[82]+this.gantt.borderColor,borderBottom:_OBS_1[82]+this.gantt.borderColor,textAlign:_OBS_1[81]});
			div.innerHTML=cal.showHead(new Date(sTime));
			if(position==-1)
			{
				if(lastAdd==null)
				{
					calDiv.insertBefore(div,calDiv.firstChild);
				}
				else if(lastAdd.nextSibling==null)
				{
					calDiv.appendChild(div);
				}
				else
				{
					calDiv.insertBefore(div,lastAdd.nextSibling);
				}
			}
			else
			{
				calDiv.appendChild(div);
			}
			lastAdd=div;
			sTime=eTime;
		}
	},clear: function()
	{
		for(var i=0;i<this.calNum;i++)
		{
			this.clearItem(i);
		}
	},clearItem: function(i)
	{
		_OBS_29(this.div.childNodes[this.calNum-i-1],true);
		delete this.cals[i];
	},moveTo: function(scale,startTime)
	{
		if(!this.drawStart){this.drawStart=startTime;}
		var point=parseInt((this.drawStart-startTime)/scale);
		if(Math.abs(point)>10000)
		{
			this.drawStart=startTime;
			var calNum=this.calNum;
			for(var i=0;i<calNum;i++)
			{
				if(!this.cals[i]){continue;}
				var p=parseInt((this.cals[i].start-this.drawStart)/scale);
				if(Math.abs(p)>10000)
				{
					this.cals[i].start=this.drawStart;
					for(var child=this.div.childNodes[i].firstChild;child;child=child.nextSibling)
					{
						child.style.left=parseInt(child.style.left+point)+"px";
					}
					p=0;
				}
				this.div.childNodes[i].style.left=p+"px";
			}
			point=0;
		}
		this.div.style.left=point+"px";
	}});
	 function SFGanttCalendarControl()
	{
	}
	SFGanttCalendarControl.prototype=new SFGanttControl()
	_OBS_4(SFGanttCalendarControl.prototype,{initialize: function(gantt)
	{
		this.gantt=gantt;
		var formats=gantt.config.getConfig("SFGanttCalendarItem/formats");
		var items={
			Minute15:	new SFGanttCalendarItem("Minute",	15,	formats.Minute15),
			Hour:		new SFGanttCalendarItem("Hour",		1,	formats.Hour),
			Hour2:		new SFGanttCalendarItem("Hour",		2,	formats.Hour2),
			Hour6:		new SFGanttCalendarItem("Hour",		6,	formats.Hour6),
			Dat:		new SFGanttCalendarItem("Dat",		1,	formats.Dat),
			Dat1:		new SFGanttCalendarItem("Dat",		1,	formats.Dat1),
			Day:		new SFGanttCalendarItem("Day",		1,	formats.Day),
			Day3:		new SFGanttCalendarItem("Dat",		3,	formats.Day3),
			Day7:		new SFGanttCalendarItem("Day",		7,	formats.Day7),
			Week:		new SFGanttCalendarItem("Week",		1,	formats.Week),
			Month:		new SFGanttCalendarItem(_OBS_1[88],	1,	formats.Month),
			Month1:		new SFGanttCalendarItem(_OBS_1[88],	1,	formats.Month1),
			Quarter:	new SFGanttCalendarItem(_OBS_1[88],	3,	formats.Quarter),
			Quarter1:	new SFGanttCalendarItem(_OBS_1[88],	3,	formats.Quarter1),
			Quarter2:	new SFGanttCalendarItem(_OBS_1[88],	6,	formats.Quarter2),
			Year:		new SFGanttCalendarItem("Year",		1,	formats.Year),
			Year1:		new SFGanttCalendarItem("Year",		1,	formats.Year1),
			Year5:		new SFGanttCalendarItem("Year",		5,	formats.Year5),
			Year10:		new SFGanttCalendarItem("Year",		10,	formats.Year10)
		};
		this.levels=[
			{scale:	3*60000/6,	cals:	[items.Minute15,items.Hour,	items.Dat]},	
			{scale:	30*60000/6,	cals:	[items.Hour2,	items.Dat,	items.Week]},	
			{scale:	3600000/6,	cals:	[items.Hour6,	items.Dat,	items.Week]},	
			{scale:	4*3600000/6,	cals:	[items.Day,	items.Week,	items.Month]},	
			{scale:	12*3600000/6,	cals:	[items.Day3,	items.Month,	items.Quarter]},
			{scale:	24*3600000/6,	cals:	[items.Day7,	items.Month,	items.Year]},	
			{scale:	96*3600000/6,	cals:	[items.Month1,	items.Quarter,	items.Year]},	
			{scale:	192*3600000/6,	cals:	[items.Month1,	items.Year,	items.Year]},	
			{scale:	576*3600000/6,	cals:	[items.Quarter1,items.Year,	items.Year5]},	
			{scale:	1728*3600000/6,	cals:	[items.Quarter2,items.Year1,	items.Year10]}	
		];
		_OBS_4(gantt,{
			getCalList:_OBS_22(this,this.getCalList)
		});
		this.listeners=[
			_OBS_28(gantt,_OBS_1[94],this,this.onScaleChange),
			_OBS_28(gantt,_OBS_1[83],this,this.onScaleChange)
		];
		this.onScaleChange();
		return true;
	},getCalList: function()
	{
		return this.calList;
	},onScaleChange: function()
	{
		var scale=this.gantt.getScale(),levels=this.levels,i;
		if(!scale){return;}
		for(i=levels.length-1;i>=0;i--){if(scale>levels[i].scale){i++;break;}}
		i=Math.min(Math.max(i,0),levels.length-1)
		this.calList=levels[i].cals;
	},remove: function()
	{
		var gantt=this.gantt;
		delete gantt.getCalList
		SFGanttControl.prototype.remove.apply(this,arguments);
	}});
	 function SFGanttChangeEventControl()
	{
	}
	SFGanttChangeEventControl.prototype=new SFGanttControl()
	_OBS_4(SFGanttChangeEventControl.prototype,{initialize: function(gantt)
	{
		if(gantt.disableChangeEvent){return false;}
		this.listeners=[
			_OBS_28(this.gantt=gantt,_OBS_1[94],this,this.onGanttInit)
		]
		return true;
	},onGanttInit: function()
	{
		var gantt=this.gantt;
		this.listeners=this.listeners.concat([
			_OBS_28(gantt.getData(),_OBS_1[143]+gantt.elementType.toLowerCase()+_OBS_1[152],this,this.onElementUpdate)
		]);
	},onElementUpdate: function(element,name,value,bValue)
	{
		var ele,elements;
		if(!(elements=this.changedElements)){elements=this.changedElements=[];}
		if(ele=_OBS_13(elements,element,function(a,b){return a.element==b}))
		{
			if(!_OBS_13(ele.fields,name))
			{
				ele.fields.push(name);
			}
		}
		else
		{
			elements.push({element:element,fields:[name]})
		}
		if(!this.eut){this.eut=window.setInterval(_OBS_22(this,this.onTime),256);}
		this.changed=true;
		this.idleTimes=0;
	},onTime: function()
	{
		if(!this.changed)
		{
			this.idleTimes++;
			if(this.idleTimes>4)
			{
				window.clearInterval(this.eut);
				delete this.eut
			}
			return;
		}
		this.changed=false;
		this.triggerUpdate();
	},triggerUpdate: function()
	{
		var element,elements=this.changedElements;
		while(element=elements.pop())
		{
			this.onElementChange(element.element,element.fields);
		}
	},onElementChange: function(element,changedFields)
	{
		var gantt=this.gantt;
		
		
		_OBS_35(this.gantt,gantt.elementType.toLowerCase()+_OBS_1[141],[element,changedFields]);
	}});
	 function SFGanttCollapseControl()
	{
	}
	SFGanttCollapseControl.prototype=new SFGanttControl()
	_OBS_4(SFGanttCollapseControl.prototype,{initialize: function(gantt,container)
	{
		if(!gantt.getLayout || gantt.disableCollapse || !gantt.getLayout(_OBS_1[80]) || !gantt.getLayout(_OBS_1[85]) || gantt.spaceWidth<4){return false;}
		var width=this.width=gantt.spaceWidth,doc=gantt.container.ownerDocument;
		var div=this.div=doc.createElement("div");
		
		_OBS_4(div.style,{position:_OBS_1[179],zIndex:200,top:'0px',width:width+"px",height:_OBS_1[177],backgroundColor:gantt.columnBarColor,borderLeft:_OBS_1[82]+gantt.borderColor,borderRight:_OBS_1[82]+gantt.borderColor});
		_OBS_11(div,_OBS_1[79]);
			var img=this.listColImg=gantt.createImage(_OBS_1[78],{position:[(width-5)/2,width]});
			_OBS_4(img.style,{position:_OBS_1[179],zIndex:200});
			_OBS_11(img,_OBS_1[183]);
			div.appendChild(img);

			var img=this.mapColImg=gantt.createImage(_OBS_1[77],{position:[(width-4)/2,width+10]});
			_OBS_4(img.style,{position:_OBS_1[179],zIndex:200});
			_OBS_11(img,_OBS_1[183]);
			div.appendChild(img);
		container.appendChild(div);
		if(gantt.setContextMenu){gantt.setContextMenu(div,function(menu){menu.type=_OBS_1[76];return true});}
		this.gantt=gantt;
		this.listeners=[
			_OBS_28(div,_OBS_1[172],this,this.onMouseDown),
			_OBS_28(gantt,_OBS_1[84],this,this.onLayoutChange)
		];
		return true;
	},onLayoutChange: function()
	{
		var gantt=this.gantt,listDiv=gantt.getLayout(_OBS_1[80]),mapDiv=gantt.getLayout(_OBS_1[85]);
		var lp=_OBS_36(listDiv,gantt.getContainer()),mp=_OBS_36(mapDiv,gantt.getContainer());
		var left=Math.max(lp[0],mp[0]);
		if((!gantt.isListShow() && left==lp[0]) || (!gantt.isChartShow() && left==mp[0]))
		{
			left=_OBS_36(listDiv.parentNode,gantt.getContainer())[0]+listDiv.parentNode.offsetWidth;
		}
		this.div.style.left=left-this.width+"px";
		gantt.setImageSrc(this.listColImg,listDiv.offsetWidth==0?_OBS_1[77]:_OBS_1[78]);
		gantt.setImageSrc(this.mapColImg,listDiv.offsetWidth==0?_OBS_1[78]:_OBS_1[77]);
	},onMouseDown: function(e)
	{
		if(_OBS_39(e)!=1){return;}
		_OBS_26(e);
		if(this.dragObj){this.onMouseUp(e);}
		var target=e.target;
		while(target && target!=this.div)
		{
			if(target==this.listColImg)
			{
				this.gantt.collapseList();
				return;
			}
			if(target==this.mapColImg)
			{
				this.gantt.collapseMap();
				return;
			}
			target=target.parentNode;
		}
		new SFDragObject(this.div,_OBS_22(this,this.onMove),{container:this.gantt.getContainer()}).onMouseDown(e);
	},onMove: function(sp,lp,type)
	{
		if(type==_OBS_1[169]){this.startColumn=this.gantt.listWidth*1}
		var listWidth=this.startColumn+lp[0]-sp[0]
		this.div.style.left=listWidth+this.gantt.idCellWidth+"px";
		if(type=="end"){this.gantt.setListWidth(listWidth)}
	}});
	 function SFGanttCursorControl()
	{
	}
	SFGanttCursorControl.prototype=new SFGanttControl()
	_OBS_4(SFGanttCursorControl.prototype,{initialize: function(gantt)
	{
		if(gantt.disableCursor){return false;}
		this.gantt=gantt;
		gantt.setCursor=_OBS_22(gantt,_OBS_81);
		return true;
	},remove: function()
	{
		var gantt=this.gantt;
		delete gantt.setCursor;
		delete this.gantt
	}});
	 function _OBS_81(obj,style)
	{
		if(style.indexOf(",")>0)
		{
			var styles=style.split(",");
			for(var i=0;i<styles.length;i++)
			{
				if(this.setCursor(obj,styles[i])){return true;}
			}
			return false;
		}
		try
		{
			if(style.toLowerCase().indexOf(".cur")>0)
			{
				style="url("+this.imgPath+"cursor/"+style+"),auto";
			}
			style=style.toLowerCase();
			if(style=="hand" && !document.all)
			{
				style=_OBS_1[183];
			}
			obj.style.cursor = style;
			return true;
		}
		catch(e){return false;}
	}
	_OBS_4(SFGanttCursorControl,{setCursor:_OBS_81});
	 function SFGanttDragResizeControl()
	{
	}
	SFGanttDragResizeControl.prototype=new SFGanttControl()
	_OBS_4(SFGanttDragResizeControl.prototype,{initialize: function(gantt,container)
	{
		
		if(gantt.disableDragResize){return false;}
		var resizeImg=this.div=gantt.createImage(_OBS_1[87]);
		_OBS_4(resizeImg.style,{position:_OBS_1[179],right:'0px',bottom:'0px',zIndex:200});
		_OBS_11(resizeImg,'se-resize');
		this.listeners=[
			_OBS_59(resizeImg,_OBS_22(this,this.onMove),{container:gantt.getContainer()})
		];
		container.appendChild(resizeImg);
		this.gantt=gantt;
		return true;
	},onMove: function(startPoint,point,type)
	{
		var gantt=this.gantt;
		if(type==_OBS_1[169]){this.startSize=gantt.getViewSize();return;}
		var size=[this.startSize[0]+point[0]-startPoint[0],this.startSize[1]+point[1]-startPoint[1]];
		if(gantt.setViewSize(size))
		{
			_OBS_4(gantt.getContainer().style,{width:size[0]+"px",height:size[1]+"px"});
		}
	}});
	 function SFGanttDragZoomControl()
	{
	}
	SFGanttDragZoomControl.prototype=new SFGanttControl()
	_OBS_4(SFGanttDragZoomControl.prototype,{initialize: function(gantt)
	{
		if(gantt.disableDragZoom || !gantt.getLayout){return false;}
		var container=gantt.getLayout("mapHead");
		if(!container){return false;}
		_OBS_11(container,_OBS_1[79]);
		this.gantt=gantt;
		this.container=container;
		this.listeners=[
			_OBS_59(container,_OBS_22(this,this.onMove),{interval:32})
		];
		return true;
	},onMove: function(sp,lp,type)
	{
		if(type==_OBS_1[169]){this.startScale=this.gantt.getScale();}
		if(lp[0]>1)
		{
			var scale=this.startScale*sp[0]/lp[0];
			this.gantt.setScale(scale);
		}
	}});
	 function SFGanttElementList(config)
	{
		_OBS_4(this,config);
	}
	SFGanttElementList.prototype=new SFGanttControl()
	_OBS_4(SFGanttElementList.prototype,{initialize: function(gantt)
	{
		if(!gantt.getLayout){return false;}
		var container=this.container=gantt.getLayout(this.mainList?"listId":_OBS_1[80]),doc=gantt.container.ownerDocument;
		if(!container){return false;}
		this.gantt=gantt;
		this.elementStyles=gantt.config.getConfigObj("SFGantt/"+gantt.elementType.toLowerCase()+"Style")
		_OBS_53(this,gantt.config.getConfigObj("SFGanttElementList"));
		if(!SFGanttElementList.listIndex){SFGanttElementList.listIndex=0;}
		this.proTag="listRow_"+(SFGanttElementList.listIndex++);
		var table=this.div=doc.createElement(_OBS_1[75]);
		_OBS_4(table,{bgColor:gantt.borderColor,border:0,cellSpacing:1,cellPadding:0});
		_OBS_4(table.style,{fontSize:'0px',position:_OBS_1[178],left:'-2px',top:'-3px',tableLayout:'fixed',zIndex:100});
		_OBS_40(table);
		
		var fRow=table.insertRow(-1),bgColor=this.bgColor;
		bgColor=bgColor?bgColor:_OBS_1[74];
		_OBS_4(fRow,{bgColor:bgColor});
		var sum=0,fields=this.fields=_OBS_84(gantt.elementType,this.fieldNames);
		var fCell=fRow.insertCell(-1);
		_OBS_4(fCell,{width:1});
		var whiteSpace=document.compatMode?'nowrap':'pre';
		_OBS_4(fCell.style,{overflowX:_OBS_1[180],fontSize:'0px',whiteSpace:whiteSpace});
		
		
		var div=doc.createElement("div");
		_OBS_4(div.style,{position:_OBS_1[178],left:'-1px',width:"1px"});
		fCell.appendChild(div);
		for(var j=0;j<fields.length;j++)
		{
			fCell=fRow.insertCell(-1);
			_OBS_4(fCell.style,{overflow:_OBS_1[180],fontSize:'0px',whiteSpace:whiteSpace});
			var width=fields[j].width;
			sum+=width+1;
			_OBS_4(fCell,{width:width});
		}
		
		var fRow=table.insertRow(-1);
		_OBS_4(fRow,{bgColor:bgColor});
		var fCell=fRow.insertCell(-1);
		
		_OBS_4(fCell,{height:(gantt.itemHeight-1)*1});
		_OBS_4(fCell.style,{overflow:_OBS_1[180],whiteSpace:whiteSpace});
		for(var j=0;j<this.fields.length;j++)
		{
			fCell=fRow.insertCell(-1);
			_OBS_4(fCell.style,{overflow:_OBS_1[180],whiteSpace:whiteSpace});
		}
		table.width=sum+3;
		this.container.appendChild(table);
		var et=this.elementType.toLowerCase();
		var listeners=this.listeners=[
			_OBS_28(gantt,_OBS_1[87],this,this.onResize),
			_OBS_28(gantt,et+"inview",this,this.drawElement),
			_OBS_28(gantt,et+"outview",this,this.clearElement),
			_OBS_28(gantt,et+_OBS_1[141],this,this.updateElement),
			_OBS_28(table,_OBS_1[73],this,this.onTableClick),
			_OBS_28(table,_OBS_1[72],this,this.onTableDblClick)
		];
		if(gantt.setContextMenu){gantt.setContextMenu(table,function(menu){menu.type=_OBS_1[71];return true});}
		listeners.push(_OBS_28(table,_OBS_1[172],this,this.onTableMouseDown));
		
		if(this.mainList)
		{
			
			if(!this.disableAdjustLineHeight && !gantt.inline){listeners.push(_OBS_28(table,_OBS_1[171],this,this.onTableMouseOver));}
		}
		else
		{
			listeners.push(_OBS_28(gantt,_OBS_1[70],this,this.onHeadMove));
			listeners.push(_OBS_28(gantt,_OBS_1[69],this,this.onHeadResize));
		}
		if(this.disableDragOrder || gantt.inline){this.mainList=null;}
		if(gantt.setCursor){gantt.setCursor(table,this.mainList?_OBS_1[68]:'fieldedit.cur,default');}
		this.onResize();
		return true;
	},setViewTop: function()
	{
		var top=this.gantt.getViewTop();
		this.div.rows[0].cells[0].firstChild.style.height=top+1+"px";
	},onResize: function()
	{
		var rows=this.div.rows,gantt=this.gantt;
		rows[rows.length-1].height=Math.max(gantt.itemHeight,gantt.viewSize[1]-gantt.headHeight-gantt.footHeight)-1;
	},applyRowStyle: function(row,element)
	{
		var className=element.ClassName;
		className=className?className:this.elementStyle;
		var elementStyle=this.elementStyles[className];
		if(elementStyle)
		{
			var style=element.Selected?elementStyle.listSelectedStyle:elementStyle.listStyle;
			if(style)
			{
				_OBS_4(row.style,style);
				return;
			}
		}
		
		var gantt=this.gantt,style=this.mainList?
			(element.Selected?{backgroundColor:gantt.listFocusColor}:{backgroundColor:this.gantt.idCellBgColor}):
			(element.Selected?{backgroundColor:gantt.listFocusColor}:{backgroundColor:_OBS_1[74]});
		_OBS_4(row.style,style);
	},drawElement: function(element,viewIndex)
	{
		if(viewIndex==0){this.setViewTop();}
		var gantt=this.gantt,drawObj=gantt.getElementDrawObj(element),height=drawObj.height;
		
		var row=this.div.insertRow(viewIndex+1);
		
		if(
			(gantt.getElementHeight(element)<=0 && !(gantt.hideSummary && gantt.inline && element.Summary && element.getFirstChild() && !element.getFirstChild().Summary)) ||
			((gantt.hideSummary && gantt.inline) && (!element.Summary && element.getParent() && element.getParent().getFirstChild()==element))
			)
		{
			row.style.display=_OBS_1[164];
		}
		if(height==0){height=gantt.itemHeight}
		
		var render=true;
		if((gantt.hideSummary && gantt.inline) && (!element.Summary && element.getParent())){render=false}
		
		this.applyRowStyle(row,element);
		
		var cell=row.insertCell(-1);
		var whiteSpace=document.compatMode?'nowrap':'pre';
		_OBS_4(cell,{height:(height-1)*1,width:1});
		cell.style.cssText="overflow:hidden;white-space:"+whiteSpace+";font-size:0px;";
		drawObj[this.proTag]=row;
		if(render){row._Element=element;}
		var doc=this.container.ownerDocument,fields=this.fields,fontSize=gantt.fontSize;
		
		for(var j=0;j<fields.length;j++)
		{
			var cell=doc.createElement("td");
			var text=[];
			text.push("overflow:hidden");
			text.push("white-space:"+whiteSpace);
			text.push("font-size:"+fontSize+"px");
			if(element.Summary){text.push("font-weight:bolder");}
			cell.style.cssText=text.join(";");
			if(render){fields[j].showBody(cell,element,this);}
			else
			{
				cell.vAlign="top";
				var div=document.createElement("div");
				_OBS_4(div.style,{width:_OBS_1[177],position:_OBS_1[178],top:'-1px',backgroundColor:_OBS_1[74],height:"1px",fontSize:'0px',overflow:_OBS_1[180]});
				cell.appendChild(div);
			}
			row.appendChild(cell);
		}
	},clearElement: function(element,viewIndex)
	{
		if(viewIndex==0){this.setViewTop();}
		this.clearInputCell();
		var drawObj=this.gantt.getElementDrawObj(element);
		_OBS_29(drawObj[this.proTag]);
		drawObj[this.proTag]=null;
	},clearInputCell: function()
	{
		if(this.focusObj && this.focusObj.inputCell>=0)
		{
			var element=this.focusObj.element;
			var field=this.fields[this.focusObj.inputCell];
			var drawObj=this.gantt.getElementDrawObj(element);
			var cells=drawObj[this.proTag].cells;
			field.showBody(cells[this.focusObj.inputCell+1],element,this);
			this.focusObj.inputCell=-1;
		}
	},onHeadMove: function(position)
	{
		this.div.style.left=position+"px";
	},onHeadResize: function(widths)
	{
		var table=this.div,cells=table.rows[0].cells,sum=0;
		for(var i=0;i<widths.length;i++)
		{
			cells[i+1].width=widths[i];
			sum+=widths[i]+1;
		}
		table.width=sum+3;
	},getEventRow: function(e)
	{
		var target=e.target;
		var row,node=target;
		while(node)
		{
			if(node.nodeName=="TR"){row=node}
			if(node==this.div){break;}
			node=node.parentNode;
		}
		if(!row || !row._Element){return;}
		return row;
	},onTableMouseOver: function(e)
	{
		var row=this.getEventRow(e);
		if(!row)
		{
			var height=_OBS_37(e,this.div)[1];
			for(row=this.div.rows[0];row;row=row.nextSibling)
			{
				height-=row.offsetHeight;
				if(height<0){break;}
			}
			if(!row || !row._Element){return;}
		}
		var element=row._Element,gantt=this.gantt;
		var size=3,height=_OBS_38(e,row)[1];
		if(height<size || height>=gantt.getElementHeight(element)-size-1)
		{
			var t=height<size?element.getPreviousView():element;
			if(t && t.canSetProperty(_OBS_1[126]))
			{
				if(gantt.setCursor){gantt.setCursor(this.div,'heightChange.cur,default');}
				this.dragMode=_OBS_1[67];
				return;
			}
		}
		if(gantt.setCursor){gantt.setCursor(this.div,element.Selected?_OBS_1[66]:_OBS_1[68]);}
		this.dragMode="";
	},onTableMouseDown: function(e)
	{
		var row=this.getEventRow(e);
		if(!row){return;}
		var element=row._Element;
		
		_OBS_35(this.gantt,this.elementType.toLowerCase()+_OBS_1[172],[element,e]);
		if(_OBS_39(e)!=1){return;}
		if(this.mainList)
		{
			if(this.dragMode==_OBS_1[67])
			{
				if(_OBS_37(e,row)[1]<3)
				{
					element=element.getPreviousView();
					if(!element){return;}
					row=this.gantt.getElementDrawObj(element)[this.proTag];
				}
			}
			this.dragElement=element;
			new SFDragObject(row,_OBS_22(this,(this.dragMode==_OBS_1[67])?this.onItemHeightMove:this.onTableMove)).onMouseDown(e);
		}
	},onItemHeightMove: function(sp,lp,type)
	{
		var element=this.dragElement,gantt=this.gantt;
		if(type==_OBS_1[169]){this.startHeight=gantt.getElementHeight(element);return;}
		var cell=gantt.getElementDrawObj(element)[this.proTag].cells[0];
		var height=Math.max(this.startHeight+lp[1]-sp[1],10);
		if(type!="end")
		{
			cell.height=height-1;
		}
		else
		{
			cell.height=this.startHeight-1;
			if(this.startHeight!=height)
			{
				element.setProperty(_OBS_1[126],height);
			}
		}
	},onTableMove: function(sp,lp,type)
	{
		if(type!="end")
		{
			var dir=lp[1]>sp[1];
			var gantt=this.gantt,element=this.dragElement;
			var distance=dir?(lp[1]-gantt.getElementHeight(element)):lp[1];
			while(element)
			{
				var newElement=dir?element.getNextView():element.getPreviousView();
				if(!newElement){break;}
				var height=gantt.getElementHeight(newElement);
				if(newElement && newElement!=this.gantt.data.getRootElement(this.elementType) && (dir?(distance-height/2):(distance+height/2))*(dir?1:-1)>0)
				{
					element=newElement;
					distance=dir?(distance-height):(distance+height);
				}
				else
				{
					break;
				}
			}
			this.dragDir=dir;
			this.flagElement=element;
			this.mainList.showElementMoveFlag(element,this.dragElement,this.dragDir);
		}
		else
		{
			if(this.flagElement && this.flagElement!=this.dragElement)
			{
				this.moveElement(this.dragElement,this.flagElement,this.dragDir);
			}
			this.mainList.showElementMoveFlag(this.dragElement,this.dragElement);
		}
		
	},showElementMoveFlag: function(element,dragElement,dir)
	{
		if(this.flagDiv)
		{
			if(element==this.flagElement){return;}
			_OBS_29(this.flagDiv);
			this.flagDiv=null;
			this.flagElement=element;
		}
		if(!element || dragElement.contains(element)){return;}
		var gantt=this.gantt,height=_OBS_36(gantt.getElementDrawObj(element)[this.proTag],this.container)[1],doc=this.container.ownerDocument;
		height=dir?(height+gantt.getElementHeight(element)-14):(height-14);
		var table=doc.createElement(_OBS_1[75]);
		table.cellSpacing=0;
		_OBS_4(table.style,{position:_OBS_1[179],width:_OBS_1[177],zIndex:200,height:'21px',left:'3px',top:height+"px"});
		var row=table.insertRow(-1);
		var cell=row.insertCell(-1);
		cell.width=3;
		var leftImg=doc.createElement("img");
		_OBS_4(leftImg.style,{width:'3px',height:'21px'});
		this.gantt.setBackgroundImage(leftImg,"dragflag_left");
		cell.appendChild(leftImg);
		var cell=row.insertCell(-1);
		this.gantt.setBackgroundImage(cell,"dragflag_right");
		this.container.appendChild(table);
		this.flagDiv=table;
	},moveElement: function(element,flagElement,dir)
	{
		var pElement,preElement=null,postElement=null;
		if(dir)
		{
			var nElement=flagElement.getNextView();
			if(!nElement || nElement.getOutlineLevel()<flagElement.getOutlineLevel())
			{
				preElement=flagElement;
			}
			else
			{
				postElement=nElement;
			}
		}
		else
		{
			var pElement=flagElement.getPreviousView();
			if(!pElement || pElement.getOutlineLevel()<=flagElement.getOutlineLevel())
			{
				postElement=flagElement;
			}
			else
			{
				preElement=pElement;
			}
		}
		var data=this.gantt.data;
		if(preElement)
		{
			data.moveElement(element.elementType,element,preElement.getParent(),preElement);
		}
		else
		{
			data.moveElement(element.elementType,element,postElement.getParent(),postElement.getPreviousSibling());
		}
	},updateElement: function(element,changedFields)
	{
		var gantt=this.gantt;
		if(element==gantt.getData().getRootElement(this.elementType)){return;}
		var drawObj=gantt.getElementDrawObj(element);
		var row=drawObj[this.proTag];
		if(!row){return;}
		if(_OBS_13(changedFields,_OBS_1[65]))
		{
			var selected=element.Selected;
			if(!selected &&this.focusObj && this.focusObj.element==element){this.clearInputCell();}
			this.applyRowStyle(row,element);
			if(this.mainList && gantt.setCursor)
			{
				gantt.setCursor(row,selected?_OBS_1[66]:_OBS_1[68]);
			}
		}
		if(_OBS_13(changedFields,_OBS_1[128]))
		{
			this.applyRowStyle(row,element);
		}
		for(var i=0;i<this.fields.length;i++)
		{
			if(!this.fields[i].checkUpdate(changedFields)){continue;}
			var cell=row.cells[i+1];
			_OBS_29(cell,true);
			var style=cell.style;
			style.fontSize=gantt.fontSize+"px";
			this.fields[i].showBody(cell,element,this);
		}
		if(_OBS_13(changedFields,_OBS_1[142]))
		{
			for(var i=0;i<this.fields.length;i++)
			{
				row.cells[i+1].style.fontWeight=element.Summary?'bolder':"";
			}
		}
	},onTableDblClick: function(e)
	{
		var row=this.getEventRow(e);
		if(!row){return;}
		var element=row._Element;
		
		var field,j,left=_OBS_37(e,row)[0],fields=this.fields;
		for(j=0;j<fields.length;j++)
		{
			left-=fields[j].width;
			if(left<0)
			{
				field=fields[j];
				break;
			}
		}
		_OBS_35(this.gantt,this.elementType.toLowerCase()+_OBS_1[72],[element,_OBS_1[71],field.Name]);
	},onTableClick: function(e)
	{
		var row=this.getEventRow(e),gantt=this.gantt;
		if(!row){if(gantt.clearSelectedElement){gantt.clearSelectedElement();}return;}
		var element=row._Element;
		_OBS_35(gantt,this.elementType.toLowerCase()+_OBS_1[73],[element,e]);
		
		if(!gantt.readOnly && !gantt.disableUpdateElement && !this.disableInput)
		{
			
			var j,left=_OBS_37(e,row)[0],fields=this.fields;
			for(j=0;j<fields.length;j++)
			{
				left-=fields[j].width;
				if(left<0){break;}
			}
			if(j==fields.length){return;}
			var field=fields[j];
			this.clearInputCell();
			if(field.inputFunc && !field.ReadOnly && (!field.inputData || element.canSetProperty(field.inputData)))
			{
				this.focusObj={inputCell:j,element:element}
				field.showInput(gantt.getElementDrawObj(element)[this.proTag].cells[j+1],element,this);
			}
		}
	}});
	 function SFGanttElementSelectControl()
	{
		this.selectedElements=[];
	}
	SFGanttElementSelectControl.prototype=new SFGanttControl()
	_OBS_4(SFGanttElementSelectControl.prototype,{initialize: function(gantt,container)
	{
		if(gantt.disableSelect){return false;}
		this.gantt=gantt;
		var elementType=gantt.elementType;
		_OBS_4(gantt,{
			getFocusElement:gantt["getFocus"+elementType]=_OBS_22(this,this.getFocusElement),
			getSelectedElements:gantt[_OBS_1[64]+elementType+'s']=_OBS_22(this,this.getSelectedElements),
			setSelectedElement:gantt[_OBS_1[63]+elementType]=_OBS_22(this,this.setSelectedElement),
			clearSelectedElement:gantt[_OBS_1[62]+elementType]=_OBS_22(this,this.clearSelectedElement)
		});
		this.listeners=[
			_OBS_28(gantt,elementType.toLowerCase()+_OBS_1[172],this,this.onElementClick),
			_OBS_28(gantt,_OBS_1[94],this,this.onGanttInit)
		];
		return true;
	},onGanttInit: function()
	{
		var gantt=this.gantt,data=gantt.getData(),el=gantt.elementType.toLowerCase();
		this.listeners=this.listeners.concat([
			_OBS_28(data,el+_OBS_1[151],this,this.onRegister),
			_OBS_28(data,el+_OBS_1[150],this,this.onUnRegister),
			_OBS_28(data,_OBS_1[143]+el+_OBS_1[141],this,this.onElementChange)
		]);
	},onRegister: function(element)
	{
		if(element.Selected){this.selectedElements.push(element);}
	},onUnRegister: function(element)
	{
		if(element.Selected){_OBS_14(this.selectedElements,element);}
	},onElementClick: function(element,e)
	{
		if(!e || _OBS_39(e)==2)
		{
			if(!element.Selected)
			{
				this.clearSelectedElement();
				element.setProperty(_OBS_1[65],true);
			}
		}
		else
		{
			var selectedElements=this.selectedElements;
			if(e.shiftKey && selectedElements[0])
			{
				var lastElement=selectedElements[selectedElements.length-1]
				var flag=this.gantt.data.compareElement(lastElement,element)>0;
				var t=lastElement;
				while(t)
				{
					t=flag?t.getNextView():t.getPreviousView();
					if(t){t.setProperty(_OBS_1[65],true);}
					if(t==element){return;}
				}
			}
			else if(e.ctrlKey)
			{
				element.setProperty(_OBS_1[65],!element.Selected);
			}
			else
			{
				this.clearSelectedElement();
				element.setProperty(_OBS_1[65],true);
			}
		}
	},onElementChange: function(element,name,value)
	{
		if(name==_OBS_1[65])
		{
			var el=this.gantt.elementType.toLowerCase();
			
			
			
			
			_OBS_35(this.gantt,el+(value?"focus":"blur"),[element]);
			if(value){this.selectedElements.push(element);}
			else{_OBS_14(this.selectedElements,element);}
		}
	},getFocusElement: function()
	{
		return this.selectedElements[this.selectedElements.length-1];
	},getSelectedElements: function()
	{
		return this.selectedElements;
	},setSelectedElement: function(element)
	{
		if(this.selectedElements && this.selectedElements[0]==element && !this.selectedElements[1]){return;}
		this.clearSelectedElement();
		element.setProperty(_OBS_1[65],true);
	},clearSelectedElement: function()
	{
		var element,elements=this.selectedElements;
		while(element=elements.pop())
		{
			element.setProperty(_OBS_1[65],false);
		}
	},remove: function()
	{
		var gantt=this.gantt;
		delete gantt.getFocusElement
		delete gantt.getSelectedElements
		delete gantt.setSelectedElement
		delete gantt.clearSelectedElement
		var elementType=gantt.elementType;
		delete gantt["getFocus"+elementType];
		delete gantt[_OBS_1[64]+elementType+'s'];
		delete gantt[_OBS_1[63]+elementType];
		delete gantt[_OBS_1[62]+elementType];
		this.selectedElements=[];
		SFGanttControl.prototype.remove.apply(this,arguments);
	}});
	 function SFGanttFieldList(fieldNames)
	{
		this.fieldNames=fieldNames;
	}
	SFGanttFieldList.prototype=new SFGanttControl()
	_OBS_4(SFGanttFieldList.prototype,{initialize: function(gantt)
	{
		if(!gantt.getLayout){return false;}
		var container=gantt.getLayout("listHead"),doc=gantt.container.ownerDocument;
		if(!container){return false;}
		var fields=this.fields=_OBS_84(gantt.elementType,this.fieldNames),table=doc.createElement(_OBS_1[75]);
		_OBS_4(this,{container:container,gantt:gantt,div:table,fieldIndex:-1});
		_OBS_4(table,{bgColor:gantt.borderColor,border:0,cellSpacing:1,cellPadding:0});
		_OBS_4(table.style,{fontSize:'0px',height:(gantt.headHeight+2)+"px",left:'-2px',top:'-1px',position:_OBS_1[178],tableLayout:'fixed'});
		var row=this.div.insertRow(-1);
		
		_OBS_4(row,{bgColor:gantt.headBgColor});
		var cell=row.insertCell(-1);
		cell.width=1;
		this.widths=[];
		for(var i=0;i<fields.length;i++)
		{
			cell=row.insertCell(-1);
			cell.vAlign="top";
			var width=fields[i].width*1;
			_OBS_4(cell.style,{overflow:_OBS_1[180],fontSize:gantt.fontSize+"px",whiteSpace:(document.compatMode?'nowrap':'pre')});
			fields[i].showHead(cell,this);
			this.widths.push(width);
		}
		container.appendChild(this.div);
		this.listeners=[
			_OBS_28(gantt,_OBS_1[94],this,this.setWidth),
			_OBS_59(table,_OBS_22(this,this.onDrag)),
			_OBS_28(table,_OBS_1[171],this,this.onMouseMove),
			_OBS_28(container,_OBS_1[91],this,this.onScroll)
		]
		return true;
	},onScroll: function()
	{
		
		_OBS_35(this.gantt,_OBS_1[70],[parseInt(this.div.style.left)]);
	},setWidth: function()
	{
		var table=this.div,cells=table.rows[0].cells,sum=0,widths=this.widths;
		for(var i=0;i<widths.length;i++)
		{
			cells[i+1].width=widths[i];
			sum+=widths[i]*1+1;
		}
		table.width=sum+3;
		
		_OBS_35(this.gantt,_OBS_1[69],[this.widths]);
	},onMouseMove: function(e)
	{
		var index=-1,left=_OBS_37(e,this.div)[0]-3,widths=this.widths;
		for(var i=0;i<widths.length;i++)
		{
			left-=widths[i];
			if(Math.abs(left)<5)
			{
				index=i;
				break;
			}
			if(left<0){break;}
		}
		this.fieldIndex=index;
		_OBS_11(this.div,index<0?_OBS_1[61]:_OBS_1[60]);
	},onDrag: function(sp,lp,type)
	{
		if(type==_OBS_1[169])
		{
			_OBS_11(this.div,_OBS_1[60]);
			this.dragNum=this.fieldIndex;
			this.dragWidth=this.widths[this.fieldIndex];
			return;
		}
		var width=Math.max(this.dragWidth+lp[0]-sp[0],20);
		this.widths[this.dragNum]=width;
		this.setWidth();
		if(type=="end"){_OBS_11(this.div,_OBS_1[61]);}
	}});
	 function SFGanttHelpLinkControl()
	{
	}
	SFGanttHelpLinkControl.prototype=new SFGanttControl()
	_OBS_4(SFGanttHelpLinkControl.prototype,{initialize: function(gantt)
	{
		var container,doc=gantt.container.ownerDocument;
		
		if(gantt.disableHelpLink || !gantt.getLayout || !(container=gantt.getLayout("head"))){return false;}
		var helpDiv=this.div=doc.createElement("div");
		_OBS_4(helpDiv.style,{position:_OBS_1[179],backgroundColor:gantt.headBgColor,width:'16px',right:'0px',top:'0px',textAlign:_OBS_1[59],padding:'3px'});
		var helpLink=doc.createElement("a");
		_OBS_4(helpLink.style,{fontSize:'24px',color:_OBS_1[58],textDecoration:_OBS_1[164]});
		helpLink.appendChild(doc.createTextNode("?"));
		helpLink.title=(window.SFNS && window.SFNS.vinfo)?_OBS_9(_OBS_7(window.SFNS.vinfo.time),'s'):"";
		_OBS_4(helpLink,{href:'<%HelpLink%>',target:'_blank'});
		helpDiv.appendChild(helpLink);
		container.appendChild(helpDiv);
		return true;
	}});
	 function SFGanttLayoutControl()
	{
		this.panels={};
	}
	SFGanttLayoutControl.prototype=new SFGanttControl()
	_OBS_4(SFGanttLayoutControl.prototype,{initialize: function(gantt,container)
	{
		this.gantt=gantt;
		this.spaceWidth=gantt.spaceWidth;
		gantt.getLayout=_OBS_22(this,this.getLayout);
		gantt.collapseMap=gantt.collapseChart=_OBS_22(this,this.collapseChart);
		gantt.collapseList=_OBS_22(this,this.collapseList);
		gantt.isListShow=_OBS_22(this,this.isListShow);
		gantt.isChartShow=_OBS_22(this,this.isChartShow);
		gantt.setListWidth=_OBS_22(this,this.setListWidth);
		this.createLayout(container);
		this.listeners=[
			_OBS_28(gantt,_OBS_1[94],this,this.onColumnResize),
			_OBS_28(gantt,"heightchange",this,this.onHeightChange),
			_OBS_28(gantt,_OBS_1[92],this,this.onGanttResize)
		]
		return true;
	},getLayout: function(name)
	{
		return this.panels[name];
	},createLayout: function(container)
	{
		var gantt=this.gantt,panels=this.panels,doc=container.ownerDocument;
		
		
		
		
		
		var listWidth=gantt.listWidth*1,mapWidth=gantt.getViewSize()[0]-listWidth-gantt.idCellWidth;
		if(listWidth<=0 || mapWidth<=0){this.spaceWidth=10;}
		
		if(gantt.headHeight>0)
		{
			var headDiv=panels.head=doc.createElement("div");
			_OBS_11(listHeadDiv,_OBS_1[61]);
			_OBS_40(headDiv);
			_OBS_4(headDiv.style,{position:_OBS_1[179],zIndex:100,left:'0px',top:'0px',width:_OBS_1[177],height:gantt.headHeight+"px",backgroundColor:gantt.headBgColor,borderBottom:_OBS_1[82]+gantt.borderColor});
				if(listWidth>0)
				{
					
					var listHeadDiv=panels.listHead=doc.createElement("div");
					_OBS_4(listHeadDiv.style,{position:_OBS_1[179],top:'0px',left:gantt.idCellWidth+"px",height:gantt.headHeight+"px",overflow:_OBS_1[180],borderLeft:_OBS_1[82]+gantt.borderColor});
					headDiv.appendChild(listHeadDiv);
				}
				if(mapWidth>0)
				{
					
					var mapHeadDiv=panels.mapHead=doc.createElement("div");
					_OBS_4(mapHeadDiv.style,{position:_OBS_1[179],top:'0px',height:gantt.headHeight+"px",top:'0px',left:'0px',width:_OBS_1[177],overflowX:_OBS_1[180],borderLeft:_OBS_1[82]+gantt.borderColor,borderRight:_OBS_1[82]+gantt.borderColor});
					headDiv.appendChild(mapHeadDiv);
				}
			container.appendChild(headDiv);
		}

		
		var bodyScrollDiv=panels.bodyScroll=doc.createElement("div");
		_OBS_4(bodyScrollDiv.style,{position:_OBS_1[179],zIndex:100,overflowY:_OBS_1[180],overflowX:_OBS_1[180],left:'0px',top:gantt.headHeight+1+"px",width:_OBS_1[177],height:(gantt.getContainer().offsetHeight-gantt.headHeight-gantt.footHeight)+"px"});
			
			var bodyDiv=panels.body=doc.createElement("div");
				if(gantt.idCellWidth>0)
				{
					
					var listIdDiv=panels.listId=doc.createElement("div");
					_OBS_4(listIdDiv.style,{position:_OBS_1[179],width:gantt.idCellWidth+"px",overflow:_OBS_1[180]});
					bodyDiv.appendChild(listIdDiv);
				}
				if(listWidth>0)
				{
					
					var listBodyDiv=panels.listBody=doc.createElement("div");
					_OBS_4(listBodyDiv.style,{position:_OBS_1[179],left:gantt.idCellWidth+"px",overflow:_OBS_1[180],borderLeft:_OBS_1[82]+gantt.borderColor,backgroundColor:_OBS_1[74]});
					bodyDiv.appendChild(listBodyDiv);
				}
				if(mapWidth>0)
				{
					
					var mapBodyDiv=panels.mapBody=doc.createElement("div");
					_OBS_40(mapBodyDiv);
					if(gantt.setContextMenu){gantt.setContextMenu(mapBodyDiv,function(menu){menu.type="chart";return true});}
					_OBS_4(mapBodyDiv.style,{position:_OBS_1[179],overflow:_OBS_1[180]});
					bodyDiv.appendChild(mapBodyDiv);
				}
			bodyScrollDiv.appendChild(bodyDiv);
		container.appendChild(bodyScrollDiv);

		
		if(gantt.footHeight>0)
		{
			var footDiv=panels.foot=doc.createElement("div");
			
			_OBS_4(footDiv.style,{position:_OBS_1[179],zIndex:100,left:'0px',bottom:'0px',width:_OBS_1[177],height:gantt.footHeight+"px",backgroundColor:gantt.bottomBgColor});
				if(listWidth>0)
				{
					
					var listFootDiv=panels.listFoot=doc.createElement("div");
					_OBS_4(listFootDiv.style,{position:_OBS_1[179],left:'0px',height:_OBS_1[177],bottom:'0px',fontSize:'0px',overflow:_OBS_1[180]});
					footDiv.appendChild(listFootDiv);
				}
				if(mapWidth>0)
				{
					
					var mapFootDiv=panels.mapFoot=doc.createElement("div");
					_OBS_4(mapFootDiv.style,{position:_OBS_1[179],height:_OBS_1[177],bottom:'0px',fontSize:'0px',overflow:_OBS_1[180]});
					footDiv.appendChild(mapFootDiv);
				}
			container.appendChild(footDiv);
		}
		return true;
	},onColumnResize: function()
	{
		var spaceW=this.spaceWidth,scrollWidth=0,panels=this.panels,gantt=this.gantt,listIdWidth=gantt.idCellWidth,listWidth;
		var listDisplay=this.listHidden?_OBS_1[164]:"";
		if(panels.listHead){panels.listHead.style.display=listDisplay;}
		if(panels.listBody){panels.listBody.style.display=listDisplay;}
		if(panels.listFoot){panels.listFoot.style.display=listDisplay;}
		var mapDisplay=this.mapHidden?_OBS_1[164]:"";
		if(panels.mapHead){panels.mapHead.style.display=mapDisplay;}
		if(panels.mapBody){panels.mapBody.style.display=mapDisplay;}
		if(panels.mapFoot){panels.mapFoot.style.display=mapDisplay;}
		if(!panels.listBody || !panels.mapBody){spaceW=0;}

		if(!panels.listBody || this.listHidden)
		{
			listWidth=0;
		}
		else if (!panels.mapBody || this.mapHidden)
		{
			listWidth=panels.bodyScroll.clientWidth-listIdWidth-spaceW;
		}
		else
		{
			listWidth=gantt.listWidth*1;
			listWidth=Math.max(listWidth,10);
		}
		var mapWidth=panels.bodyScroll.clientWidth-listWidth-listIdWidth-spaceW;
		if(panels.mapBody && mapWidth-scrollWidth<10)
		{
			listWidth+=mapWidth-scrollWidth-10;
			mapWidth=10+scrollWidth;
		}
		
		if(!this.listHidden)
		{
			if(panels.listBody){panels.listBody.style.width=listWidth+"px";}
			if(panels.listHead){panels.listHead.style.width=listWidth+"px";}
			if(panels.listFoot){panels.listFoot.style.width=listWidth+listIdWidth+"px";}
		}
		if(!this.mapHidden)
		{
			if(panels.mapHead){panels.mapHead.style.left=listIdWidth+listWidth+spaceW+"px";}
			if(panels.mapHead){panels.mapHead.style.width=mapWidth-scrollWidth+"px";}
			if(panels.mapBody){panels.mapBody.style.left=listWidth+spaceW+listIdWidth+"px";}
			if(panels.mapBody){panels.mapBody.style.width=mapWidth-scrollWidth+"px";}
			if(panels.mapFoot){panels.mapFoot.style.left=listWidth+spaceW+listIdWidth+"px";}
			if(panels.mapFoot){panels.mapFoot.style.width=mapWidth-scrollWidth+"px";}
		}
		
		_OBS_35(gantt,_OBS_1[84]);
	},onGanttResize: function(size)
	{
		var gantt=this.gantt;
		this.panels.bodyScroll.style.height=(size[1]-gantt.headHeight-gantt.footHeight)+"px";
		this.onColumnResize();
	},collapseList: function()
	{
		if(!this.listHidden && this.mapHidden)
		{
			this.collapseChart();
		}
		this.listHidden=!this.listHidden;
		this.onColumnResize();
	},collapseChart: function()
	{
		if(!this.mapHidden && this.listHidden)
		{
			this.collapseList();
		}
		this.mapHidden=!this.mapHidden;
		this.onColumnResize();
	},isListShow: function()
	{
		return !this.listHidden;
	},isChartShow: function()
	{
		return !this.mapHidden;
	},setListWidth: function(width)
	{
		var gantt=this.gantt;
		width=Math.max(width,0);
		width=Math.min(width,this.panels.bodyScroll.clientWidth-gantt.idCellWidth-10);
		gantt.listWidth=width;
		this.onColumnResize();
	},onHeightChange: function(bodyHeight)
	{
		var panels=this.panels,height=(bodyHeight+64)+"px";
		if(panels.mapBody){panels.mapBody.style.height=height;}
		else if(panels.body){panels.body.style.height=height;}
	},remove: function()
	{
		var gantt=this.gantt;
		delete gantt.getLayout
		delete gantt.collapseMap
		delete gantt.collapseList
		delete gantt.isListShow
		delete gantt.isChartShow
		delete gantt.setListWidth
		var panels=this.panels;
		for(var key in panels)
		{
			_OBS_29(panels[key]);
		}
		this.panels={};
		SFGanttControl.prototype.remove.apply(this,arguments);
	}});
	 function SFGanttLinksMap()
	{
	}
	SFGanttLinksMap.prototype=new SFGanttControl()
	_OBS_4(SFGanttLinksMap.prototype,{initialize: function(gantt)
	{
		if(gantt.disableLinksMap || !gantt.getMapPanel){return false;}
		_OBS_53(this,gantt.config.getConfigObj("SFGanttLinksMap"));
		_OBS_4(this,{
			gantt:gantt,
			taskHeight:12,	
			arrayPadding:10,					
			linkStyles:gantt.config.getConfigObj("SFGantt/linkStyle")
		});
		this.taskPadding=gantt.itemHeight-this.taskHeight;
		
		var linksDiv=this.div=gantt.container.ownerDocument.createElement("div");
		_OBS_4(linksDiv.style,{position:_OBS_1[179],fontSize:'0px',top:'-1px',left:'0px',zIndex:200});
		gantt.getMapPanel().appendChild(linksDiv);
		this.linkNoticeFields=this.linkNoticeFields?_OBS_88(this.linkNoticeFields.split(",")):null;
		this.listeners=[
			_OBS_28(gantt,_OBS_1[94],this,this.onInit),
			_OBS_28(gantt,_OBS_1[83],this,this.onScale),
			_OBS_28(gantt,_OBS_1[57],this,this.drawLinks),
			_OBS_28(gantt,_OBS_1[56],this,this.clearLinks),
			_OBS_28(gantt,_OBS_1[55],this,this.updateLinks),
			_OBS_28(gantt,_OBS_1[54],this,this.onScale),
			_OBS_28(linksDiv,_OBS_1[73],this,this.onLinkClick),
			_OBS_28(linksDiv,_OBS_1[72],this,this.onLinkDblClick)
		];
		if(this.linkNoticeFields && gantt.setTooltip)
		{
			gantt.setTooltip(linksDiv,_OBS_22(this,this.getTooltip))
		}
		this.inited=false;
		this.onInit();
		return true;
	},onInit: function()
	{
		var data=this.gantt.getData();
		if(!data || this.inited){return;}
		this.listeners=this.listeners.concat([
			_OBS_28(data,_OBS_1[154],this,this.drawLink),
			_OBS_28(data,_OBS_1[153],this,this.clearLink),
			_OBS_28(data,_OBS_1[98],this,this.updateLink),
			_OBS_28(data,_OBS_1[99],this,this.onScale),
			_OBS_28(data,_OBS_1[101],this,this.onScale)
		]);
		this.inited=true;
		this.refresh();
	},onScale: function()
	{
		var viewTasks=this.gantt.getViewElements();
		if(!viewTasks){return}
		for(var i=0;i<viewTasks.length;i++)
		{
			this.clearLinks(viewTasks[i],0);
		}
		this.changed=true;
		this.idleTimes=0;
		if(!this.gantt.forPrint)
		{
			if(!this.refreshTimeout){this.refreshTimeout=window.setInterval(_OBS_22(this,this.onTime),100);}
		}
		else
		{
			this.refresh();
		}
	},onTime: function()
	{
		if(!this.changed)
		{
			this.idleTimes++;
			if(this.idleTimes>4)
			{
				window.clearInterval(this.refreshTimeout);
				delete this.refreshTimeout
				this.refresh();
			}
			return;
		}
		this.changed=false;
	},refresh: function()
	{
		
		var viewTasks=this.gantt.getViewElements();
		if(!viewTasks){return}
		if(this.refreshTimeout){window.clearTimeout(this.refreshTimeout);}
		this.refreshTimeout=null;
		for(var i=0;i<viewTasks.length;i++)
		{
			this.drawLinks(viewTasks[i],i);
		}
	},drawLinks: function(task,viewIndex)
	{
		var links=task.getPredecessorLinks();
		for(var i=0;i<links.length;i++)
		{
			this.drawLink(links[i],true);
		}
		var links=task.getSuccessorLinks();
		for(var i=0;i<links.length;i++)
		{
			this.drawLink(links[i],false);
		}
	},clearLinks: function(task,viewIndex)
	{
		var links=task.getPredecessorLinks();
		for(var i=0;i<links.length;i++)
		{
			this.clearLink(links[i]);
		}
		var links=task.getSuccessorLinks();
		for(var i=0;i<links.length;i++)
		{
			this.clearLink(links[i]);
		}
	},updateLinks: function(task,changedFields)
	{
		var redrawAllLink=false,redrawLink=false;
		for(var i=0;i<changedFields.length;i++)
		{
			var field=changedFields[i];
			if(field==_OBS_1[127])
			{
				redrawAllLink=true;
				break;
			}
			if(!redrawLink && (field==_OBS_1[139] || field==_OBS_1[140]))
			{
				redrawLink=true;
			}
		}
		if(redrawAllLink)
		{
			this.onScale();
		}
		else if(redrawLink)
		{
			this.clearLinks(task,0);
			this.drawLinks(task);
		}
	},updateLink: function(link)
	{
		if(this.gantt.getElementDrawObj(link))
		{
			this.clearLink(link);
			this.drawLink(link);
		}
	},drawLink: function(link)
	{
		if(this.refreshTimeout){return;}
		var gantt=this.gantt,objTask=link.PredecessorTask,task=link.SuccessorTask,scale=gantt.getScale();
		if(gantt.getElementDrawObj(link).linkImg){return;}
		
		if(!scale || objTask.isHidden() || task.isHidden()){return;}
		var drawObj=gantt.getElementDrawObj(link);
		if(!objTask.Start || !objTask.Finish || !task.Start || !task.Finish){return;}
		var sOffset=[gantt.getMapPanelPosition(objTask.Start),gantt.getElementViewTop(objTask)+2];
		var eOffset=[gantt.getMapPanelPosition(task.Start),gantt.getElementViewTop(task)+2];
		
		var lineOffset;
		if(gantt.getElementHeight(objTask)==0 && (lineOffset=gantt.getElementHeight(objTask)-gantt.getElementDrawObj(objTask).height)!=0){sOffset[1]+=lineOffset;}
		if(gantt.getElementHeight(task)==0 && (lineOffset=gantt.getElementHeight(task)-gantt.getElementDrawObj(task).height)!=0){eOffset[1]+=lineOffset;}

		var sWidth=(objTask.Finish-objTask.Start)/scale;
		var eWidth=(task.Finish-task.Start)/scale;
		var dir,arrowSize,position,points=[],arrowStyle={},lineStyle={borderStyle:_OBS_1[167]},color=_OBS_1[58];
		var className=link.ClassName;
		className=className?className:this.linkStyle;
		var linkStyle=this.linkStyles[className];
		if(linkStyle)
		{
			if(linkStyle.color){color=linkStyle.color;}
			if(linkStyle.lineStyle){lineStyle=linkStyle.lineStyle;}
		}
		lineStyle.borderColor=color;
		switch(parseInt(link.Type))
		{
			case 0:
				dir=_OBS_1[95];
				arrowSize=[5,9];
				var asLeft=sOffset[0]+sWidth;
				var asTop=sOffset[1]+Math.ceil((this.taskHeight+this.taskPadding)/2);
				var aeLeft=eOffset[0]+eWidth;
				var aeTop=eOffset[1]+Math.ceil((this.taskHeight+this.taskPadding)/2);
				var aLeft=Math.max(asLeft,aeLeft)+this.arrayPadding;
				points.push([asLeft,asTop]);
				points.push([aLeft,asTop]);
				points.push([aLeft,aeTop]);
				points.push([aeLeft,aeTop]);
				position=[aeLeft,aeTop-Math.floor(arrowSize[1]/2)];
				break;
			case 2:
				dir=_OBS_1[95];
				arrowSize=[5,9];
				
				var asLeft=sOffset[0];
				var asTop=sOffset[1]+Math.ceil((this.taskHeight+this.taskPadding)/2);
				var aeLeft=eOffset[0]+eWidth+arrowSize[0];
				var aeTop=eOffset[1]+Math.ceil((this.taskHeight+this.taskPadding)/2);
				points.push([asLeft,asTop]);
				points.push([asLeft-this.arrayPadding,asTop]);
				points.push([asLeft-this.arrayPadding,eOffset[1]]);
				points.push([eOffset[0]+eWidth+this.arrayPadding,eOffset[1]]);
				points.push([eOffset[0]+eWidth+this.arrayPadding,aeTop]);
				points.push([aeLeft,aeTop]);
				position=[aeLeft-arrowSize[0],aeTop-Math.floor(arrowSize[0]/2)];
				break;
			case 3:
				dir=_OBS_1[59];
				arrowSize=[5,9];
				var asLeft=sOffset[0];
				var asTop=sOffset[1]+Math.ceil((this.taskHeight+this.taskPadding)/2);
				var aeLeft=eOffset[0];
				var aeTop=eOffset[1]+Math.ceil((this.taskHeight+this.taskPadding)/2);
				var aLeft=Math.min(asLeft,aeLeft)-this.arrayPadding;
				points.push([asLeft,asTop]);
				points.push([aLeft,asTop]);
				points.push([aLeft,aeTop]);
				points.push([aeLeft,aeTop]);
				position=[aeLeft-arrowSize[0],aeTop-Math.floor(arrowSize[1]/2)];
				break;
			case 1:
			default:
				var asLeft=sOffset[0]+sWidth;
				var asTop=sOffset[1]+Math.ceil((this.taskHeight+this.taskPadding)/2);
				if(objTask.Finish<=task.Start && eOffset[1]!=sOffset[1])
				{
					dir=sOffset[1]>eOffset[1]?"up":"down";
					arrowSize=[9,5];
					
					var aeLeft=eOffset[0];
					if(objTask.Finish.valueOf()==objTask.Start.valueOf()){asTop-=3;}
					var aeTop=sOffset[1]>eOffset[1]?(eOffset[1]+this.taskPadding/2+this.taskHeight):(eOffset[1]+this.taskPadding/2-arrowSize[1]);
					if(task.Finish.valueOf()-task.Start.valueOf()==0){aeTop-=3;}
					if(task.Finish.valueOf()-task.Start.valueOf()!=0 && objTask.Finish.valueOf()!=objTask.Start.valueOf())
					{
						aeLeft=Math.max(aeLeft,asLeft+5);
					}
					points.push([asLeft,asTop]);
					points.push([aeLeft,asTop]);
					points.push([aeLeft,aeTop]);
					position=[(aeLeft-Math.floor(arrowSize[0]/2)-1),aeTop];
				}
				else
				{
					dir=_OBS_1[59];
					arrowSize=[5,9];
					
					var aeLeft=eOffset[0]-arrowSize[0];
					var aeTop=eOffset[1]+(this.taskPadding+this.taskHeight)/2;
					points.push([asLeft,asTop]);
					if(eOffset[1]!=sOffset[1])
					{
						points.push([asLeft+this.arrayPadding,asTop]);
						points.push([asLeft+this.arrayPadding,eOffset[1]]);
						points.push([eOffset[0]-this.arrayPadding,eOffset[1]]);
						points.push([eOffset[0]-this.arrayPadding,aeTop]);
					}
					points.push([aeLeft,aeTop]);
					position=[aeLeft,aeTop-Math.floor(arrowSize[1]/2)];
				}
				break;
		}
		drawObj.linkPaths=this.getLinkPaths(points,lineStyle,link);
		
		var linkImg=gantt.createImage("arrow_"+dir,{color:color,position:position});
		_OBS_4(linkImg.style,arrowStyle);
		_OBS_4(linkImg.style,{position:_OBS_1[179],fontSize:'0px'});
		drawObj.linkImg=linkImg;
		linkImg._link=link;
		this.div.appendChild(linkImg);
	},getLinkPaths: function(points,linkStyle,link)
	{
		var paths=[],doc=this.gantt.container.ownerDocument;
		for(var i=1;i<points.length;i++)
		{
			var div=doc.createElement("div");
			_OBS_4(div.style,linkStyle);
			_OBS_4(div.style,{position:_OBS_1[179],fontSize:'0px',borderWidth:'0px'});
			if(points[i-1][0]==points[i][0])
			{
				_OBS_4(div.style,{borderRightWidth:"1px",height:Math.abs(points[i][1]-points[i-1][1])+"px",width:0+"px",left:(points[i][0]-1)+"px",top:(Math.min(points[i][1],points[i-1][1]))+"px"});
			}
			else if(points[i-1][1]==points[i][1])
			{
				_OBS_4(div.style,{borderTopWidth:"1px",width:Math.abs(points[i][0]-points[i-1][0])+"px",height:0+"px",left:(Math.min(points[i][0],points[i-1][0]))+"px",top:(points[i][1])+"px"});
			}
			this.div.appendChild(div);
			div.aaa='bbb'
			div._link=link;
			paths.push(div);
		}
		return paths;
	},clearLink: function(link)
	{
		var drawObj=this.gantt.getElementDrawObj(link);
		if(!drawObj){return}
		if(drawObj.linkImg)
		{
			_OBS_29(drawObj.linkImg);
			drawObj.linkImg._link=null;
			drawObj.linkImg=null;
		}
		if(drawObj.linkPaths)
		{
			var p;
			while(p=drawObj.linkPaths.pop())
			{
				p._link=null;
				_OBS_29(p);
			}
			drawObj.linkPaths=null;
		}
		this.gantt.removeElementDrawObj(link)
	},onLinkClick: function(e)
	{
		var link=e.target._link;
		if(!link){return;}
		
		_OBS_35(this.gantt,"linkclick",[link,e]);
	},onLinkDblClick: function(e)
	{
		var link=e.target._link;
		if(!link){return;}
		_OBS_26(e);
		
		_OBS_35(this.gantt,"linkdblclick",[link]);
	},getTooltip: function(tooltip,e)
	{
		var link=e.target._link,doc=this.gantt.container.ownerDocument;
		if(!link){return;}
		if(tooltip.bindObject==link){return false;}
		var table=doc.createElement(_OBS_1[75]);
		table.style.fontSize="12px";
		var row=table.insertRow(-1);
		var cell=row.insertCell(-1);
		_OBS_4(cell,{align:_OBS_1[81],colSpan:2,noWrap:true});
		cell.appendChild(doc.createTextNode(this.tooltipTitle.link));
		for(var i=0;i<this.linkNoticeFields.length;i++)
		{
			row=table.insertRow(-1);
			cell=row.insertCell(-1);
			_OBS_4(cell,{align:_OBS_1[95],noWrap:true});
			this.linkNoticeFields[i].showHead(cell);
			cell=row.insertCell(-1);
			_OBS_4(cell,{align:_OBS_1[95],noWrap:true});
			this.linkNoticeFields[i].showBody(cell,link,this);
		}
		tooltip.bindObject=link;
		tooltip.setContent(table);
		return true;
	},remove: function()
	{
		this.onScale();
		if(this.refreshTimeout){window.clearInterval(this.refreshTimeout);}
		SFGanttControl.prototype.remove.apply(this,arguments);
	},depose: function()
	{
		if(this.delayTimeout){window.clearTimeout(this.delayTimeout);}
		_OBS_29(this.div);
		_OBS_33(this);
		for(var key in this){this[key]=null;}
	}});
	 function SFGanttProgressLine(time,config)
	{
		this.time=time?_OBS_7(time):new Date();
		this.progressType="normal";
		_OBS_4(this,{vertexSize:[11,11],lineColor:'red',lineWeight:1});
		_OBS_4(this,config);
	}
	SFGanttProgressLine.prototype=new SFGanttControl()
	_OBS_4(SFGanttProgressLine.prototype,{initialize: function(gantt)
	{
		if(!gantt.getMapPanel){return false;}
		_OBS_53(this,gantt.config.getConfigObj("SFGanttProgressLine"));
		if(!SFGanttProgressLine.listIndex){SFGanttProgressLine.listIndex=0;}
		this.proTag="progressLine_"+(SFGanttProgressLine.listIndex++);
		_OBS_4(this,{
			gantt:gantt,
			taskHeight:12,	
			lineStyle:gantt.config.getConfigObj("SFGanttProgressLine/lineStyle")
		});
		this.taskPadding=gantt.itemHeight-this.taskHeight;
		
		var linesDiv=this.div=gantt.container.ownerDocument.createElement("div");
		_OBS_4(linesDiv.style,{position:_OBS_1[179],fontSize:'0px',top:'-1px',left:'0px',zIndex:190});
		gantt.getMapPanel().appendChild(linesDiv);
		this.listeners=[
			_OBS_28(gantt,_OBS_1[83],this,this.onScale),
			_OBS_28(gantt,_OBS_1[57],this,this.drawLine),
			_OBS_28(gantt,_OBS_1[56],this,this.clearLine),
			_OBS_28(gantt,_OBS_1[55],this,this.updateLine),
			_OBS_28(gantt,_OBS_1[54],this,this.onScale)
		];
		if(this.lineNoticeFields && gantt.setTooltip)
		{
			gantt.setTooltip(linesDiv,_OBS_22(this,this.getTooltip))
		}
		this.onScale();
		return true;
	},getGraphics: function()
	{
		var graphics=[SFGraphicsSvg,SFGraphicsVml,SFGraphicsCanvas,SFGraphicsDiv];
		for(var i=0;i<graphics.length;i++)
		{
			if(graphics[i].isSupport())
			{
				return new graphics[i]();
			}
		}
		return new SFGraphics(true);
	},onScale: function()
	{
		var viewTasks=this.gantt.getViewElements();
		if(!viewTasks){return}
		for(var i=0;i<viewTasks.length;i++)
		{
			this.clearLine(viewTasks[i],0);
		}
		if(!this.refreshTimeout){this.refreshTimeout=window.setInterval(_OBS_22(this,this.onTime),100);}
		this.changed=true;
		this.idleTimes=0;
	},onTime: function()
	{
		if(!this.changed)
		{
			this.idleTimes++;
			if(this.idleTimes>4)
			{
				window.clearInterval(this.refreshTimeout);
				delete this.refreshTimeout
				this.refresh();
			}
			return;
		}
		this.changed=false;
	},refresh: function()
	{
		
		var viewTasks=this.gantt.getViewElements();
		if(!viewTasks){return}
		if(this.refreshTimeout){window.clearTimeout(this.refreshTimeout);}
		this.refreshTimeout=null;
		for(var i=0;i<viewTasks.length;i++)
		{
			this.drawLine(viewTasks[i],i);
		}
	},hasVertex: function(task)
	{
		return  task.Start && 
			task.Finish && 
			task.Start<=this.time &&
			(task.PercentComplete!=100 || (task.Finish>=this.time));
	},getVertexTime: function(task)
	{
		var percentComplete=task.PercentComplete?task.PercentComplete:0,time=task.Start.valueOf()+(task.Finish-task.Start)*percentComplete/100;
		switch(this.progressType)
		{
			case "earlier":
				time=Math.min(time,this.time);
				break;
			case "later":
				time=Math.max(time,this.time);
				break;
		}
		return time;
	},drawLine: function(task)
	{
		if(this.refreshTimeout){return;}
		var gantt=this.gantt,scale=gantt.getScale();
		if(!scale){return}
		var drawObj=gantt.getElementDrawObj(task);
		if(drawObj[this.proTag]){return;}
		if(!this.hasVertex(task)){return;}

		
		var objTask=task.getPreviousView();
		while(objTask)
		{
			if(this.hasVertex(objTask)){break;}
			objTask=objTask.getPreviousView();
		}
		var sp,ep;
		if(!objTask)
		{
			sp=[gantt.getMapPanelPosition(this.time),0];
		}
		else
		{
			
			sp=[gantt.getMapPanelPosition(this.getVertexTime(objTask)),gantt.getElementViewTop(objTask)+gantt.getElementDrawObj(objTask).height/2];
		}
		var ep=[gantt.getMapPanelPosition(this.getVertexTime(task)),gantt.getElementViewTop(task)+gantt.getElementDrawObj(task).height/2];
		
		var vertexSize=this.vertexSize,vImg=gantt.createImage(this.vertexImg?this.vertexImg:"task_head_4",{color:this.vertexColor?this.vertexColor:_OBS_1[53],size:this.vertexSize})
		_OBS_4(vImg.style,{position:_OBS_1[179],fontSize:'0px',left:(ep[0]-Math.floor(vertexSize[0]/2))+"px",top:(ep[1]-Math.floor(vertexSize[1]/2))+"px"});
		drawObj[this.proTag]=vImg;
		this.div.appendChild(vImg);
		
		var graphics=this.getGraphics();
		graphics.setLineColor(this.lineColor);
		graphics.setLineWeight(this.lineWeight);
		drawObj[this.proTag+"_l"]=graphics.div;
		var op={x:Math.min(sp[0],ep[0]),y:Math.min(sp[1],ep[1])}
		graphics.setPosition(op);
		graphics.start({x:0,y:0},1,{x:Math.abs(sp[0]-ep[0]),y:Math.abs(sp[1]-ep[1])});
		graphics.moveTo({x:sp[0]-op.x,y:sp[1]-op.y});
		graphics.lineTo({x:ep[0]-op.x,y:ep[1]-op.y});
		graphics.finish();
		graphics._task=task;
		this.div.appendChild(graphics.div);
	},clearLine: function(task,viewIndex)
	{
		var drawObj=this.gantt.getElementDrawObj(task);
		if(!drawObj){return}
		if(drawObj[this.proTag])
		{
			_OBS_29(drawObj[this.proTag]);
			delete drawObj[this.proTag];
			_OBS_29(drawObj[this.proTag+"_l"]);
			delete drawObj[this.proTag+"_l"];
		}
	},updateLine: function(task,changedFields)
	{
		var redrawAll=false,redraw=false;
		for(var i=0;i<changedFields.length;i++)
		{
			var field=changedFields[i];
			if(field==_OBS_1[127])
			{
				redrawAll=true;
				break;
			}
			if(!redraw && (field==_OBS_1[139] || field==_OBS_1[140]  || field==_OBS_1[134]))
			{
				redraw=true;
			}
		}
		if(redrawAll)
		{
			this.onScale();
		}
		else if(redraw)
		{
			
			var lastTask=task.getNextView();
			while(lastTask)
			{
				if(this.hasVertex(lastTask)){break;}
				lastTask=lastTask.getNextView();
			}
			if(lastTask)
			{
				this.clearLine(lastTask);
				this.drawLine(lastTask);
			}
			this.clearLine(task);
			this.drawLine(task);
		}
	},remove: function()
	{
		this.onScale();
		if(this.refreshTimeout){window.clearInterval(this.refreshTimeout);}
		delete this.refreshTimeout;
		_OBS_29(this.div);
		SFGanttControl.prototype.remove.apply(this,arguments);
	},depose: function()
	{
		for(var key in this){this[key]=null;}
	}});
	 function SFGanttListScrollNotice()
	{
	}
	SFGanttListScrollNotice.prototype=new SFGanttControl()
	_OBS_4(SFGanttListScrollNotice.prototype,{initialize: function(gantt,container)
	{
		if(gantt.disableListScrollNotice || !gantt.getLayout){return false;}
		_OBS_53(this,gantt.config.getConfigObj("SFGanttListScrollNotice"));
		var elementType=gantt.elementType;
		this.fields=this[elementType.toLowerCase()+"Fields"]?_OBS_84(elementType,this[elementType.toLowerCase()+"Fields"].split(",")):null;
		this.gantt=gantt;
		
		var div=this.div=container.ownerDocument.createElement("div");
		_OBS_4(div.style,{position:_OBS_1[179],zIndex:400,display:_OBS_1[164]});
		_OBS_4(div.style,this.divStyle);
		container.appendChild(div);
		this.listeners=[
			_OBS_28(gantt,_OBS_1[91],this,this.onScroll),
			_OBS_28(gantt,_OBS_1[92],this,this.onResize)
		];
		this.onResize();
		return true;
	},onScroll: function(scrollTop,scrollObj)
	{
		if(!scrollObj || !scrollObj.spanElements[1]){return;}
		if(!this.timeout){this.timeout=window.setInterval(_OBS_22(this,this.onTime),64);}
		this.scrollObj=scrollObj;
		this.changed=true;
		this.idleTimes=0;
	},onTime: function()
	{
		if(!this.changed)
		{
			this.idleTimes++;
			if(this.idleTimes>16)
			{
				this.div.style.display=_OBS_1[164];
				window.clearInterval(this.timeout);
				delete this.timeout
			}
			return;
		}
		this.changed=false;
		
		var task=this.scrollObj.spanElements[1],fieldLength=this.fields.length,doc=this.div.ownerDocument;
		if(!this.div.firstChild)
		{
			var table=doc.createElement(_OBS_1[75]);
			this.div.appendChild(table);
			table.width=160;
			table.style.fontSize="12px";
			for(var i=0;i<fieldLength;i++)
			{
				var row=table.insertRow(-1);
				var cell=row.insertCell(-1);
				cell.width=60;
				this.fields[i].showHead(cell,this);
				var cell=row.insertCell(-1);
				if(i==0){cell.width=100;}
				var div=doc.createElement("div");
				_OBS_4(div.style,{position:_OBS_1[178],overflow:_OBS_1[180],width:"100px",height:"14px"});
				cell.appendChild(div);
			}
		}
		for(var i=0;i<fieldLength;i++)
		{
			var div=this.div.firstChild.rows[i].cells[1].firstChild;
			_OBS_29(div,true);
			this.fields[i].showBody(div,task,this);
		}
		this.div.style.display="";
	},onResize: function()
	{
		_OBS_4(this.div.style,{right:'30px',top:(this.gantt.headHeight+10)+"px"});
	}});
	 function SFGanttLogoControl()
	{
	}
	SFGanttLogoControl.prototype=new SFGanttControl()
	_OBS_4(SFGanttLogoControl.prototype,{initialize: function(gantt)
	{
		var container;
		if(!gantt.getLayout || !(container=gantt.getLayout("head"))){return false;}
		this.gantt=gantt;
		var logo=this.div=gantt.createImage("logo",{size:[gantt.idCellWidth,gantt.headHeight]});
		_OBS_4(logo.style,{position:_OBS_1[179]});
		if(gantt.setTooltip){gantt.setTooltip(logo,_OBS_22(this,this.getLogoTooltip));}
		container.appendChild(logo);
		if(gantt.setContextMenu){gantt.setContextMenu(logo,function(menu){menu.type="logo";return true});}
		return true;
	},getLogoTooltip: function(tooltip)
	{
		if(tooltip && tooltip.bindObject==this){return false;}
		var div=this.div.ownerDocument.createElement("div")
		div.innerHTML=_OBS_3("7xm1w2r4_iMRBlTFxdWowBF4B2QgCgzd");
		tooltip.setContent(div);
		tooltip.bindObject=this;
		return true;
	}});
	 function SFGanttContextMenuControl()
	{
		this.contextMenuItems=[];
	}
	SFGanttContextMenuControl.prototype=new SFGanttControl()
	_OBS_4(SFGanttContextMenuControl.prototype,{initialize: function(gantt)
	{
		if(gantt.disableContextMenu){return false;}
		_OBS_53(this,gantt.config.getConfigObj("SFMenu"));
		this.gantt=gantt;
		var container=this.container=gantt.getContainer(),doc=gantt.container.ownerDocument;
		var table=this.div=doc.createElement(_OBS_1[75]);
		_OBS_4(table,{cellSpacing:0,border:0,cellPadding:0});
		_OBS_4(table.style,{position:_OBS_1[179],zIndex:700});
		_OBS_4(table.style,this.tableStyle);
		_OBS_4(gantt,{
			addContextMenuItem:_OBS_22(this,this.addItem),
			getContextMenuItemById:_OBS_22(this,this.getItemById),
			removeContextMenuItem:_OBS_22(this,this.removeItem),
			setContextMenu:_OBS_22(this,this.setContextMenu)
		});
		this.listeners=[
			_OBS_31(container,_OBS_1[52],_OBS_26),
			_OBS_28(container,_OBS_1[172],this,this.onMouseDown),
			_OBS_28(table,_OBS_1[171],this,this.onItemMouseOver),
			_OBS_28(table,_OBS_1[172],this,this.onItemClick),
			_OBS_28(doc,_OBS_1[172],this,this.hidden)
		];
		return true;
	},setContextMenu: function(div,handle)
	{
		if(!div._SF_E_){div._SF_E_=[];}
		div._SF_E_.contextMenu=handle;
	},onMouseDown: function(e)
	{
		var btn=_OBS_39(e);
		if(btn==4){_OBS_27(e);}
		if(btn!=2){return;}
		var target=e.target;
		while(target)
		{
			if(target._SF_E_ && target._SF_E_ .contextMenu && target._SF_E_ .contextMenu(this,e))
			{
				_OBS_26(e);
				if(this.items){this.hidden();}
				var items=[],allItems=this.contextMenuItems;
				for(var i=0;i<allItems.length;i++)
				{
					var type=allItems[i].showHandle(this,e);
					if(type==1){items.push(allItems[i]);}
				}
				this.items=items;
				var position=_OBS_38(e,this.container);
				this.show(position);
				return;
			}
			target=target.parentNode;
		}
	},addItem: function(showHandle,runHandle,text,icon,id,index)
	{
		if(id)
		{
			for(var i=0;i<this.contextMenuItems.length;i++)
			{
				if(id==this.contextMenuItems[i].id==id){return false;}
			}
		}
		var menuItem=new SFMenuItem(showHandle,runHandle,text,icon,id,index);
		this.contextMenuItems.push(menuItem);
		return menuItem;
	},getItemById: function(id)
	{
		for(var i=0;i<this.contextMenuItems.length;i++)
		{
			if(id==this.contextMenuItems[i].id)
			{
				return this.contextMenuItems[i];
			}
		}
		return null;
	},removeItem: function(id)
	{
		if(typeof(id)==_OBS_1[184]){id=id.id;}
		if(id)
		{
			for(var i=0;i<this.contextMenuItems.length;i++)
			{
				if(this.contextMenuItems[i].id==id)
				{
					return this.contextMenuItems.splice(i,1);
				}
			}
		}
		return null;
	},show: function(position)
	{
		var container=this.container,table=this.div;
		this.createItemContent();
		container.appendChild(table);
		var left=position[0]+1,top=position[1]+1;
		if(left+table.offsetWidth>container.offsetWidth){left=position[0]-table.offsetWidth-1;}
		if(top+table.offsetHeight>container.offsetHeight){top=position[1]-table.offsetHeight-1;}
		_OBS_4(table.style,{left:left+"px",top:top+"px"});
	},hidden: function()
	{
		this.focusObj=null;
		var items=this.items;
		if(items)
		{
			for(var i=0;i<items.length;i++)
			{
				items[i].row=null;
			}
			this.items=null;
		}
		_OBS_29(this.div,true);
		if(this.div.parentNode==this.container)
		{
			this.container.removeChild(this.div);
		}
	},createItemContent: function()
	{
		this.items.sort(function(a,b){if(a.index==b.index){return 0;}return a.index>b.index?1:-1;});
		var doc=this.container.ownerDocument;
		for(var i=0;i<this.items.length;i++)
		{
			var item=this.items[i];
			var row=this.div.insertRow(-1);
			var cell=row.insertCell(-1);
			_OBS_4(cell,{width:34,height:24,bgColor:'#F6F6F6',align:_OBS_1[81]});
			if(item.icon)
			{
				var img=this.gantt.createImage(item.icon,{size:[16,16]});
				cell.appendChild(img);
			}
			cell=row.insertCell(-1);
			_OBS_4(cell,{noWrap:'true'});
			_OBS_4(cell.style,{paddingLeft:'10px',paddingRight:'25px',fontSize:'13px',cursor:_OBS_1[61]});
			cell.innerHTML=item.text;
			item.row=row;
		}
	},getFocusItem: function(e)
	{
		if(!this.items){return null;}
		var target=e.target,row,table=this.div;
		while(target)
		{
			if(target==table){break;}
			if(target.nodeName=="TR"){row=target}
			target=target.parentNode;
		}
		if(!row){return null;}
		for(var i=table.rows.length-1;i>=0;i--)
		{
			if(row==table.rows[i])
			{
				return this.items[i];
			}
		}
		return null;
	},onItemMouseOver: function(e)
	{
		var item=this.getFocusItem(e);
		if(!item){return;}
		var focusObj=this.focusObj;
		if(focusObj)
		{
			focusObj.row.style.backgroundColor="";
			focusObj.row.cells[0].style.backgroundColor='#F6F6F6';
		}
		this.focusObj=item;
		item.row.style.backgroundColor="#C4E0F2";
		item.row.cells[0].style.backgroundColor="#C4E0F2";
	},onItemClick: function(e)
	{
		var item=this.getFocusItem(e);
		if(!item){return;}
		_OBS_26(e);
		this.hidden();
		if(item.runHandle){item.runHandle(this);}
	},remove: function()
	{
		this.hidden();
		var gantt=this.gantt;
		delete gantt.addContextMenuItem
		delete gantt.getContextMenuItemById
		delete gantt.removeContextMenuItem
		delete gantt.setContextMenu
		delete this.contextMenuItems
		SFGanttControl.prototype.remove.apply(this,arguments);
	}});
	 function SFGanttDefaultMenuControl()
	{
	}
	SFGanttDefaultMenuControl.prototype=new SFGanttControl()
	_OBS_4(SFGanttDefaultMenuControl.prototype,{initialize: function(gantt,container)
	{
		if(!gantt.addContextMenuItem){return;}
		var names=gantt.config.getConfig("SFGantt/menuText");
		
		
		gantt.addContextMenuItem(
			function(ma){return (ma.type=="chart" && ma.gantt.zoomIn)?1:0},
			function(ma){ma.gantt.zoomIn();},
			names.ZoomIn,
			'icon_zoomin',
			"ZoomIn",
			551);
		
		gantt.addContextMenuItem(
			function(ma){return (ma.type=="chart" && ma.gantt.zoomOut)?1:0},
			function(ma){ma.gantt.zoomOut();},
			names.ZoomOut,
			'icon_zoomout',
			"ZoomOut",
			556);
		
		gantt.addContextMenuItem(
			function(ma){return (ma.type==_OBS_1[71] && ma.gantt.focusIntoView && ma.gantt.getFocusElement && ma.gantt.getFocusElement() && ma.gantt.getFocusElement().Start)?1:0},
			function(ma){ma.gantt.focusIntoView();},
			names.FocusIntoView,
			"icon_taskgoto",
			"FocusIntoView",
			601);
		
		gantt.addContextMenuItem(
			
			function(ma){return (ma.type==_OBS_1[71] && ma.gantt.addTask && !ma.gantt.readOnly && !ma.gantt.disableAddTask)?1:0},
			function(ma){ma.gantt.addTask();},
			names.AddTask,
			null,
			"AddTask",
			651);
		
		gantt.addContextMenuItem(
			function(ma){return (ma.type==_OBS_1[71] && ma.gantt.deleteTask && !ma.gantt.readOnly && !ma.gantt.disableDeleteTask && ma.gantt.getFocusElement && ma.gantt.getFocusElement() && ma.gantt.getFocusElement().elementType==_OBS_1[160])?1:0},
			function(ma){ma.gantt.deleteTask();},
			names.DeleteTask,
			null,
			'DeleteTask',
			656);
		
		gantt.addContextMenuItem(
			function(ma){return (ma.type==_OBS_1[71] && ma.gantt.addTasksLinks && !ma.gantt.readOnly && !ma.gantt.disableAddLink && ma.gantt.getFocusElement && ma.gantt.getFocusElement() && ma.gantt.getFocusElement().elementType==_OBS_1[160])?1:0},
			function(ma){ma.gantt.addTasksLinks();},
			names.AddTasksLinks,
			null,
			"AddTasksLinks",
			701);
		
		gantt.addContextMenuItem(
			function(ma){return (ma.type==_OBS_1[71] && ma.gantt.removeTasksLinks && !ma.gantt.readOnly && !ma.gantt.disableDeleteLink && ma.gantt.getFocusElement && ma.gantt.getFocusElement() && ma.gantt.getFocusElement().elementType==_OBS_1[160])?1:0},
			function(ma){ma.gantt.removeTasksLinks();},
			names.RemoveTasksLinks,
			null,
			'RemoveTasksLinks',
			706);
		
		gantt.addContextMenuItem(
			function(ma){return (ma.type==_OBS_1[71] && ma.gantt.upgradeSelectedTasks && !ma.gantt.readOnly && !ma.gantt.disableUpdateTask && ma.gantt.getFocusElement && ma.gantt.getFocusElement() && ma.gantt.getFocusElement().elementType==_OBS_1[160])?1:0},
			function(ma){ma.gantt.upgradeSelectedTasks();},
			names.UpgradeTask,
			null,
			'UpgradeTask',
			751);
		
		gantt.addContextMenuItem(
			function(ma){return (ma.type==_OBS_1[71] && ma.gantt.degradeSelectedTasks && !ma.gantt.readOnly && !ma.gantt.disableUpdateTask && ma.gantt.getFocusElement && ma.gantt.getFocusElement() && ma.gantt.getFocusElement().elementType==_OBS_1[160])?1:0},
			function(ma){ma.gantt.degradeSelectedTasks();},
			names.DegradeTask,
			null,
			'DegradeTask',
			756);
		
		gantt.addContextMenuItem(
			function(ma){return (ma.gantt.showPrintDialog)?1:0},
			function(ma){ma.gantt.showPrintDialog();},
			names.Print,
			"icon_print",
			"Print",
			791);
		
		gantt.addContextMenuItem(
			function(ma){return (ma.type==_OBS_1[76] && ma.gantt.collapseChart && !ma.gantt.isChartShow())?1:0},
			function(ma){ma.gantt.collapseChart();},
			names.ShowChart,
			null,
			'ShowChart',
			801);
		
		gantt.addContextMenuItem(
			function(ma){return (ma.type==_OBS_1[76] && ma.gantt.collapseChart && ma.gantt.isChartShow())?1:0},
			function(ma){ma.gantt.collapseChart();},
			names.HideChart,
			null,
			'HideChart',
			806);
		
		gantt.addContextMenuItem(
			function(ma){return (ma.type==_OBS_1[76] && ma.gantt.collapseList && !ma.gantt.isListShow())?1:0},
			function(ma){ma.gantt.collapseList();},
			names.ShowList,
			null,
			'ShowList',
			850);
		
		gantt.addContextMenuItem(
			function(ma){return (ma.type==_OBS_1[76] && ma.gantt.collapseList && ma.gantt.isListShow())?1:0},
			function(ma){ma.gantt.collapseList();},
			names.HideList,
			null,
			'HideList',
			856);
		
		gantt.addContextMenuItem(
			function(ma){return (ma.type=="logo")?1:0},
			function(ma){window.open(_OBS_3("ef_aTS_EGQjTle3iE5BYP9sRk91KkKKIUAW7XNsWDbu4WgkR"));},
			names.Help,
			null,
			'Help',
			901);
		
		gantt.addContextMenuItem(
			function(ma){return (ma.type=="logo")?1:0},
			function(ma){window.open(_OBS_3("ef_aTS_EGQjTle3iE5BYP9sRk91KkKKIUAW7XNsWDV"));},
			names.About,
			null,
			'About',
			951);
		return true;
	}});
	 function SFGanttDialogControl()
	{
	}
	SFGanttDialogControl.prototype=new SFGanttControl()
	_OBS_4(SFGanttDialogControl.prototype,{initialize: function(gantt)
	{
		if(this.gantt){return false;}
		gantt.openDialog=_OBS_22(this,this.openDialog);
		gantt.closeDialog=_OBS_22(this,this.closeDialog);
		this.gantt=gantt;
		return true;
	},openDialog: function(content,config)
	{
		if(this.isOpen){this.closeDialog();}
		config=config?config:{};
		var gantt=this.gantt,viewSize=gantt.getViewSize(),container=this.gantt.getContainer();
		var size=config.size?config.size:[parseInt(viewSize[0]/2),parseInt(viewSize[1]/2)];
		var wSize=[size[0]+10,size[1]+35];
		var div=this.div,contentDiv;
		if(!div)
		{
			var div=this.div=document.createElement("div");
			_OBS_4(div.style,{position:_OBS_1[179],overflow:_OBS_1[180],zIndex:990,border:_OBS_1[51],backgroundColor:_OBS_1[74]});
			var titleDiv=document.createElement("div");
			_OBS_4(titleDiv.style,{position:_OBS_1[178],borderBottom:_OBS_1[51],backgroundColor:_OBS_1[50],width:_OBS_1[177],height:'21px'});
			div.appendChild(titleDiv);
			var titleSpan=document.createElement("div");
			_OBS_4(titleSpan.style,{position:_OBS_1[178],width:_OBS_1[177],height:'16px',fontSize:"14px",fontWeight:'bolder',padding:'4px',paddingLeft:'10px',cursor:_OBS_1[144]});
			titleDiv.appendChild(titleSpan);
			var closeBtn=document.createElement("div")
			_OBS_4(closeBtn.style,{position:_OBS_1[179],right:'2px',top:'-8px',fontSize:'25px',backgroundColor:_OBS_1[50],cursor:_OBS_1[183]});
			closeBtn.appendChild(document.createTextNode("×"));
			div.appendChild(closeBtn);
			this.listeners=[_OBS_28(closeBtn,_OBS_1[73],this,this.closeDialog)];
			var contentDiv=document.createElement("div");
			_OBS_4(contentDiv.style,{position:_OBS_1[178],fontSize:'13px',margin:'5px'});
			div.appendChild(contentDiv);
		}
		else
		{
			contentDiv=div.lastChild;
		}
		_OBS_4(div.style,{left:(viewSize[0]-wSize[0])/2+"px",top:(viewSize[1]-wSize[1])/2+"px",width:wSize[0]+"px",height:wSize[1]+"px"});
		_OBS_4(contentDiv.style,{width:size[0]+"px",height:size[1]+"px"});
		
		if(config.title)
		{
			div.firstChild.firstChild.innerHTML=config.title;
		}
		else
		{
			_OBS_29(div.firstChild.firstChild,true);
		}
		
		if(typeof(content)==_OBS_1[184])
		{
			contentDiv.appendChild(content);
		}
		else
		{
			contentDiv.innerHTML=content;
		}
		container.appendChild(div);
		
		if(config.isModal)
		{
			
			var maskDiv=this.maskDiv;
			if(!maskDiv)
			{
				var maskDiv=this.maskDiv=document.createElement("div");
				_OBS_4(maskDiv.style,{position:_OBS_1[179],zIndex:950,backgroundColor:_OBS_1[58]});
				_OBS_12(maskDiv,0.7);
			}
			_OBS_4(maskDiv.style,{left:'0px',top:'0px',width:viewSize[0]+"px",height:viewSize[1]+"px"});
			container.appendChild(maskDiv);
		}
		else
		{
			if(this.maskDiv){container.removeChild(this.maskDiv);}
		}
		
		 this.isOpen=true;
		_OBS_35(this.gantt,"dialogopen",[config]);
	},closeDialog: function()
	{
		var container=this.gantt.getContainer();
		if(this.maskDiv){container.removeChild(this.maskDiv);}
		if(this.div){
			while(this.div.lastChild.firstChild){this.div.lastChild.removeChild(this.div.lastChild.firstChild);}
			container.removeChild(this.div);
		}
		
		this.isOpen=false;
		_OBS_35(this.gantt,"dialogclose");
	},remove: function()
	{
		this.closeDialog();
		var gantt=this.gantt;
		delete gantt.openDialog
		delete gantt.closeDialog
		delete this.maskDiv
		SFGanttControl.prototype.remove.apply(this,arguments);
	}});
	 function SFGanttPrintControl()
	{
	}
	SFGanttPrintControl.prototype=new SFGanttControl()
	_OBS_4(SFGanttPrintControl.prototype,{initialize: function(gantt)
	{
		this.gantt=gantt;
		gantt.createPrintWindow=_OBS_22(this,this.createPrintWindow);
		gantt.addPrintContent=_OBS_22(this,this.addPrintContent);
		gantt.printContentWindow=_OBS_22(this,this.printContentWindow);
		gantt.showPrintDialog=_OBS_22(this,this.showPrintDialog);
		return true;
	},showPrintDialog: function()
	{
		var gantt=this.gantt,contentDiv=this.div;
		if(!contentDiv)
		{
			contentDiv=this.div=document.createElement("div");
			contentDiv.style.padding='5px';
			contentDiv.style.fontSize="12px";

			var div=document.createElement("div");
			div.style.margin='5px';
			div.appendChild(document.createTextNode(_OBS_3("7AK3wnDF,CgVBFD8GHw")));
			var cb_hor=this.cb_hor=document.createElement(_OBS_1[49]);
			cb_hor.type=_OBS_1[48];
			div.appendChild(cb_hor);
			div.appendChild(document.createTextNode(_OBS_3("7CGQwoPm,BsvBE1d")));
			contentDiv.appendChild(div);
			
			var div=document.createElement("div");
			div.style.margin='5px';
			div.appendChild(document.createTextNode(_OBS_3("7AK3wnDF,xghBG5mGHw")));
			var cb_showList=this.cb_showList=document.createElement(_OBS_1[49]);
			cb_showList.type=_OBS_1[48];
			div.appendChild(cb_showList);
			cb_showList.checked=true;
			div.appendChild(document.createTextNode(_OBS_3("7zCMwaH6,xozC0HV")));
			var cb_showMap=this.cb_showMap=document.createElement(_OBS_1[49]);
			cb_showMap.type=_OBS_1[48];
			div.appendChild(cb_showMap);
			cb_showMap.checked=true;
			div.appendChild(document.createTextNode(_OBS_3("7wiZwaH6,y,aC0HV")));
			contentDiv.appendChild(div);

			var div=document.createElement("div");
			div.style.margin='5px';
			div.appendChild(document.createTextNode(_OBS_3("7AK3wnDF_h2fBFvhGHw")));
			var cb_all=this.cb_all=document.createElement(_OBS_1[49]);
			cb_all.type=_OBS_1[48];
			div.appendChild(cb_all);
			div.appendChild(document.createTextNode(_OBS_3("7AK3wnDF,CcQB1jXxOK0wT_Q")));
			contentDiv.appendChild(div);

			var div=document.createElement("div");
			div.style.margin='5px';
			div.innerHTML=_OBS_3("8iidw1zo,,2cBXX6yvO1wCVVW,LblOKDUN,EceOIe9M5dyzHoErbSj7amTgJVZWNZYxygrOtmK8tnZNBaIyCpwHMe27");
			contentDiv.appendChild(div);
			
			var div=document.createElement("div");
			div.style.margin='5px';
			div.align=_OBS_1[81];
			var bt_submit=document.createElement(_OBS_1[49]);
			bt_submit.type="button";
			bt_submit.value=_OBS_3("7AK3wnDF");
			div.appendChild(bt_submit);
			var bt_cancel=document.createElement(_OBS_1[49]);
			bt_cancel.type="button";
			bt_cancel.value=_OBS_3("7wi6w4nd");
			div.appendChild(bt_cancel);
			contentDiv.appendChild(div);

			this.listeners=[

				_OBS_28(bt_submit,_OBS_1[73],this,this.onSubmit),
				_OBS_28(bt_cancel,_OBS_1[73],gantt,gantt.closeDialog)
			];
		}
		gantt.openDialog(contentDiv,{isModal:true,size:[280,120],title:_OBS_3("7AK3wnDF")});


	},onSubmit: function()
	{

		var width=210,height=297;
		var isHor=this.cb_hor.checked,isAll=this.cb_all.checked;
		var showList=this.cb_showList.checked,showMap=this.cb_showMap.checked;
		if(!showList && !showMap){return;}
		
		width-=19.05+19.05;
		height-=19.05+19.05+20;
		var dpi=window.chrome?96:96,inch=25.4;
		var gantt=this.gantt,win=gantt.createPrintWindow(),size=isHor?[height/inch*dpi,width/inch*dpi]:[width/inch*dpi,height/inch*dpi],padding=20;
		var lastElement=gantt.getData().getRootElement(gantt.elementType).getLastDescendant(true);
		var maxHeight=gantt.getElementViewTop(lastElement)+gantt.getElementHeight(lastElement),currentHeight=isAll?0:gantt.getLayout(_OBS_1[47]).scrollTop,nextHeight=0;
		var listWidth,mapWidth,currentTime,nextTime,maxTime=gantt.getData().getRootTask().Finish;
		for(var i=0;;i++)
		{
			if(currentHeight>=maxHeight || (!isAll && i>0)){break;}
			nextHeight=Math.min(currentHeight+size[1]-gantt.headHeight-padding,maxHeight);
			currentTime=isAll?gantt.getData().getRootTask().Start:gantt.getStartTime();
			for(var j=0;;j++)
			{
				if(j==0)
				{
					listWidth=(!showList)?0:((!showMap)?size[0]:gantt.listWidth);
					mapWidth=size[0]-listWidth-10-gantt.idCellWidth;
				}
				else
				{
					listWidth=0;
					mapWidth=size[0]-gantt.idCellWidth;
				}
				nextTime=new Date(currentTime.valueOf()+mapWidth*gantt.getScale());
				this._addPrintContent(win,[size[0],Math.min(size[1],nextHeight-currentHeight+gantt.headHeight+padding)],isHor,[listWidth,mapWidth],currentTime,currentHeight);
				currentTime=nextTime;
				if(currentTime>=maxTime || !showMap || !isAll){break;}
			}
			currentHeight=nextHeight;
		}
	},createPrintWindow: function()
	{
		if(this._win){this.deposePrintWindow(this._win);delete this._win;}
		var iframe=document.createElement("iframe");
		_OBS_4(iframe.style,{position:_OBS_1[179],width:"1px",height:"1px",left:'-2px',top:'-2px',visibility:_OBS_1[180]});
		this.gantt.getContainer().appendChild(iframe);
		var win = iframe.contentWindow;
		win.location="about:blank";
		var doc=win.document;
		var html = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n";
		html += "<html>";
		html += _OBS_3("TeFLPldTKf5BlyFNSsBjTrkSjETQmLB1LQ0KZswQVnCpZwlHNLsCiipajDcPL2kYSnr7DGI7TmXEAW1EPWgPdv,jQFVHHMKOQP4bHsBYUvgSWSLtaeSDYhJYHdsLVr84K6URfvAAgvo");
		html += "<body style=\"padding:0px;margin:0px;\" bgcolor=\"#FFFFFF\"></body></html>";
		doc.writeln(html);
		doc.close();
		win.gantts=[];
		if(!document.all){this._win=win;}
		return win;
	},deposePrintWindow: function(win)
	{
		var g;
		while(g=win.gantts.pop()){g.depose();}
		_OBS_29(win.frameElement);
	},_addPrintContent: function()
	{
		if(!this.printList)
		{
			this.printList=[];
			this.printTimeout=setInterval(_OBS_22(this,function(){
				var argu=this.printList.shift();
				this.addPrintContent.apply(this,argu);
				this.gantt.openDialog(_OBS_3('Te_PUxOIYgrIh9_PRLtdUrkghEnMXLRKXhZXX6gURbyDJ6oShwLaPPrO3mGAeACIopup')+this.printList.length+_OBS_3('Mm4ifS7EUfrSXM'),{isModal:true,size:[200,32],title:_OBS_3('7CaJwo97,SY5BUj7xNWCwBldAFYsCCHO')});
				if(!this.printList[0])
				{
					clearInterval(this.printTimeout);
					delete this.printTimeout;
					delete this.printList;
					argu[0].frameElement.style.visibility="";
					this.printContentWindow(argu[0]);
					this.gantt.closeDialog();
				}
			}),32);
		}
		this.printList.push(arguments);
	},addPrintContent: function(win,size,isHor,widths,startTime,startHeight)
	{
		var doc=win.document,body=doc.body,gantt=this.gantt,div=doc.createElement("div");
		if(body.firstChild){div.style.pageBreakBefore="always";}
		div.style.border=_OBS_1[51];
		body.appendChild(div);
		var container=div;
		if(isHor)
		{
			_OBS_15(div,[size[1],size[0]]);
			container=doc.createElement("div");
			_OBS_15(container,size);
			_OBS_17(container,90);
			if(!container.style.filter){
			container.style.top=(size[0]-size[1])/2+"px";
			container.style.left=-(size[0]-size[1])/2+"px";}
			div.appendChild(container);
		}
		else
		{
			_OBS_15(div,size);
		}
		var gtConfig=new SFConfig();
		_OBS_52(gtConfig,gantt.config,true);
		gtConfig.setConfig("SFGantt/container",container);	
		gtConfig.setConfig("SFGantt/readOnly",true);
		gtConfig.setConfig("SFGantt/footHeight",0);
		gtConfig.setConfig("SFGantt/listWidth",widths[0]);
		gtConfig.setConfig("SFGantt/disableTooltip",true);
		gtConfig.setConfig("SFGantt/disableChangeEvent",true);
		gtConfig.setConfig("SFGantt/disableHelpLink",true);
		gtConfig.setConfig("SFGantt/disableTimeScrollNotice",true);
		gtConfig.setConfig("SFGantt/disableDragResize",true);
		gtConfig.setConfig("SFGantt/disableCursor",true);
		gtConfig.setConfig("SFGantt/disableContextMenu",true);
		gtConfig.setConfig("SFGantt/disableScroll",true);
		gtConfig.setConfig("SFGantt/disableSelect",true);
		gtConfig.setConfig("SFGantt/scrollTop",startHeight);
		gtConfig.setConfig("SFGantt/forPrint",true);
		var g=new SFGantt(gtConfig,gantt.data);
		g.showMap(startTime,gantt.getZoom());
		win.gantts.push(g);
	},printContentWindow: function(win)
	{
		window.setTimeout(_OBS_22(this,function()
		{
			win.focus();
			win.print();
			if(document.all){this.deposePrintWindow(win);}
		}),0);
	},remove: function()
	{
		delete this.a_pageType;
		delete this.t_width;
		delete this.t_height;
		var gantt=this.gantt;
		delete gantt.createPrintWindow;
		delete gantt.addPrintContent;
		SFGanttControl.prototype.remove.apply(this,arguments);
	}});
	 function SFGanttScrollControl()
	{
	}
	SFGanttScrollControl.prototype=new SFGanttControl()
	_OBS_4(SFGanttScrollControl.prototype,{initialize: function(gantt)
	{
		this.gantt=gantt;
		if(gantt.disableScroll || !gantt.getLayout || !gantt.showScroller){return false;}
		var container=gantt.getLayout(_OBS_1[47]);
		container.style.overflowY=_OBS_1[91];
		if(!container){return false;}
		this.listeners=[
			_OBS_28(container,_OBS_1[91],this,this.onScroll)
		]
		return true;
	},onScroll: function(e)
	{
		_OBS_26(e);
		if(!this.timeout){this.timeout=window.setInterval(_OBS_22(this,this.onTime),128);}
		var scrollObj=this.scrollObj?this.scrollObj:this.getScrollObj();
		scrollObj.scrollTop=this.gantt.getLayout(_OBS_1[47]).scrollTop;
		scrollObj.changed=true;
		scrollObj.idleTimes=0;
	},onTime: function()
	{
		var scrollObj=this.scrollObj,gantt=this.gantt;
		if(!scrollObj || !scrollObj.changed)
		{
			if(scrollObj)
			{
				scrollObj.idleTimes++;
				if(scrollObj.idleTimes>8)
				{
					window.clearInterval(this.timeout);
					delete this.timeout
				}
			}
			return;
		}
		scrollObj.changed=false;
		if(gantt.getTooltip){gantt.getTooltip().hidden();}
		var scrollTop=scrollObj.scrollTop;
		this.updateScroll(scrollObj,1,scrollTop);
		this.updateScroll(scrollObj,3,scrollTop+this.gantt.getLayout(_OBS_1[47]).clientHeight*1.5);
		
		_OBS_35(this.gantt,_OBS_1[91],[scrollTop,scrollObj]);
	},updateScroll: function(scrollObj,index,scrollTop)
	{
		var gantt=this.gantt,element=scrollObj.spanElements[index];
		var distance=scrollTop-scrollObj.spanHeights[index];
		var dir=distance>0;
		while(element)
		{
			if(dir)
			{
				if(distance<gantt.getElementHeight(element)){break;}
				var newElement=element.getNextView();
				if(!newElement){break;}
				gantt.getElementDrawObj(newElement);
				element=newElement;
				distance-=gantt.getElementHeight(element);
			}
			else
			{
				if(distance>0){break;}
				var newElement=element.getPreviousView();
				if(!newElement){break;}
				gantt.getElementDrawObj(newElement);
				element=newElement;
				distance+=gantt.getElementHeight(newElement);
			}
		}
		scrollObj.spanHeights[index]=scrollTop-distance;
		scrollObj.spanElements[index]=element;
	},getScrollObj: function()
	{
		var gantt=this.gantt,element=gantt.getViewElements()[0],height=gantt.getViewTop();
		return this.scrollObj={
			lastTime:new Date().valueOf(),
			spanElements:[element,element,element,element],
			spanHeights:[height,height,height,height]
		};
	}});
	 function SFGanttScrollerControl()
	{
	}
	SFGanttScrollerControl.prototype=new SFGanttControl()
	_OBS_4(SFGanttScrollerControl.prototype,{initialize: function(gantt)
	{
		if(!this.layoutName ||!gantt.getLayout){return false;}
		var container=gantt.getLayout(this.layoutName);
		if(!container){return false;}
		_OBS_40(container);
		var doc=container.ownerDocument,div=doc.createElement("div");
		_OBS_4(div.style,{position:_OBS_1[179]});
		
		var leftImg=gantt.createImage(_OBS_1[46]);
		_OBS_4(leftImg.style,{position:_OBS_1[179],left:'0px'});
		div.appendChild(leftImg);

		var rightImg=gantt.createImage(_OBS_1[45]);
		_OBS_4(rightImg.style,{position:_OBS_1[179],right:'0px'});
		div.appendChild(rightImg);

		var barDiv=doc.createElement("div");
		_OBS_4(barDiv.style,{position:_OBS_1[179]});
		
		var barLeftImg=gantt.createImage(_OBS_1[44]);
		_OBS_4(barLeftImg.style,{position:_OBS_1[179],left:'0px'});
		barDiv.appendChild(barLeftImg);
		
		var barRightImg=gantt.createImage(_OBS_1[43]);
		_OBS_4(barRightImg.style,{position:_OBS_1[179],right:'0px'});
		barDiv.appendChild(barRightImg);
		
		var barCenterDiv=document.createElement("div");
		gantt.setBackgroundImage(barCenterDiv,_OBS_1[42]);
		_OBS_4(barCenterDiv.style,{position:_OBS_1[179],left:'3px',textAlign:_OBS_1[81]});

		var barCenterImg=gantt.createImage(_OBS_1[41]);
		barCenterDiv.appendChild(barCenterImg);
		
		barDiv.appendChild(barCenterDiv);
		div.appendChild(barDiv);
		container.appendChild(div);
		_OBS_4(this,{
			gantt:gantt,
			container:container,
			div:div,
			leftImg:leftImg,
			rightImg:rightImg,
			barDiv:barDiv,
			barLeftImg:barLeftImg,
			barRightImg:barRightImg,
			barCenterDiv:barCenterDiv,
			barCenterImg:barCenterImg
		});
		this.listeners=[
			_OBS_28(div,_OBS_1[172],this,this.onMouseDown),
			_OBS_28(gantt,_OBS_1[84],this,this.onResize)
		];
		this.scrollLeft=0;
		return true;
	},onResize: function()
	{
		if(!this.container){return;}
		var container=this.container,width=container.offsetWidth,height=container.offsetHeight,size=this.size;
		if(size && size[1]==height && size[0]==width){return;}
		if(width<=0)
		{
			this.div.style.display=_OBS_1[164];
			return;
		}
		else
		{
			this.div.style.display="";
		}
		_OBS_15(this.div,[width,height]);
		this.div.style.display=width-height*2<=0?_OBS_1[164]:"";
		if(width-height*2<=0){return;}
		_OBS_15(this.barDiv,[width-height*2,height]);
		_OBS_15(this.barCenterDiv,[Math.max(0,width-height*2-6),height]);
		if(!size || size[1]!=height)
		{
			_OBS_15(this.leftImg,[height,height]);
			_OBS_15(this.rightImg,[height,height]);
			_OBS_15(this.barLeftImg,[3,height]);
			_OBS_15(this.barRightImg,[3,height]);
			_OBS_15(this.barCenterImg,[8,height]);
		}
		this.size=[width,height];
		this.init(this.offsetWidth,this.scrollWidth,this.scrollLeft);
	},init: function(offsetWidth,scrollWidth,scrollLeft)
	{
		if(!offsetWidth || !scrollWidth){return;}
		var width=this.size[0]-this.size[1]*2;
		this.offsetWidth=offsetWidth;
		this.scrollWidth=scrollWidth;
		this.barDiv.style.display=offsetWidth<scrollWidth?"":_OBS_1[164]
		var bWidth=Math.max(scrollWidth?parseInt(width*offsetWidth/scrollWidth):0,14);
		_OBS_4(this.barDiv.style,{width:bWidth+"px"});
		_OBS_4(this.barCenterDiv.style,{width:bWidth-6+"px"});
		this.width=width-bWidth;
		this.scrollTo(scrollLeft?scrollLeft:this.scrollLeft,false);
	},scrollTo: function(scrollLeft,trigger)
	{
		scrollLeft=this.scrollLeft=Math.max(Math.min(scrollLeft,this.scrollWidth-this.offsetWidth),0);
		_OBS_4(this.barDiv.style,{left:(this.size[1]+scrollLeft/(this.scrollWidth-this.offsetWidth)*this.width)+"px"});
		if(trigger!=false)
		{
			
			_OBS_35(this,_OBS_1[91],[scrollLeft]);
		}
	},onMouseDown: function(e)
	{
		_OBS_26(e);
		if(this.pressObj || this.spaceObj){this.onMouseUp(e);}
		var gantt=this.gantt,div=this.div,doc=div.ownerDocument;
		if(div.setCapture){div.setCapture();}
		switch(e.target)
		{
			case this.leftImg:
			case this.rightImg:
				var flag=(this.rightImg==e.target);
				gantt.setImageSrc(e.target,"scroll_"+(flag?_OBS_1[59]:_OBS_1[95])+'1');
				var pressObj=this.pressObj={
					dir:(flag?1:-1),
					timeout:window.setInterval(_OBS_22(this,this.onButtonPress),32),
					ul:_OBS_28(doc,_OBS_1[170],this,this.onMouseUp)
				};
				_OBS_35(this,_OBS_1[40],[this.scrollLeft]);
				this.onButtonPress();
				break;
			case this.div:
				var point=_OBS_37(e,this.div);
				var toLeft=point[0]/(this.size[0]-this.size[1]*2)*(this.scrollWidth-this.offsetWidth);
				var spaceObj=this.spaceObj={
					toLeft:toLeft,
					timeout:window.setInterval(_OBS_22(this,this.onSpacePress),128),
					ul:_OBS_28(doc,_OBS_1[170],this,this.onMouseUp)
				};
				_OBS_35(this,_OBS_1[40],[this.scrollLeft]);
				this.onSpacePress();
				break;
			default:
				new SFDragObject(div,_OBS_22(this,this.onBarMove),{interval:32}).onMouseDown(e)
				break;
		}
	},onMouseUp: function(e)
	{
		_OBS_26(e);
		if(e && e.target && e.target.ownerDocument.releaseCapture){e.target.ownerDocument.releaseCapture();}
		var gantt=this.gantt;
		if(this.pressObj)
		{
			var pressObj=this.pressObj;
			_OBS_35(this,_OBS_1[39],[this.scrollLeft]);
			window.clearInterval(pressObj.timeout);
			_OBS_32(pressObj.ul);
			gantt.setImageSrc(this.leftImg,_OBS_1[46]);
			gantt.setImageSrc(this.rightImg,_OBS_1[45]);
			this.pressObj=null;
		}
		if(this.spaceObj)
		{
			var spaceObj=this.spaceObj;
			_OBS_35(this,_OBS_1[39],[this.scrollLeft]);
			this.scrollTo(spaceObj.toLeft);
			window.clearInterval(spaceObj.timeout);
			_OBS_32(spaceObj.ul);
			this.spaceObj=null;
		}
	},onButtonPress: function()
	{
		if(!this.pressObj){return;}
		this.scrollTo(this.scrollLeft+this.pressObj.dir*8);
	},onSpacePress: function()
	{
		if(!this.spaceObj){return;}
		var spaceObj=this.spaceObj,toLeft=spaceObj.toLeft,point=this.scrollLeft;
		var offset=spaceObj.toLeft-this.scrollLeft;
		if(Math.abs(offset)<64){this.onMouseUp();return;}
		this.scrollTo(this.scrollLeft+(offset>0?64:-64));
	},onBarMove: function(sp,lp,type)
	{
		var gantt=this.gantt;
		if(type==_OBS_1[169])
		{
			gantt.setImageSrc(this.barLeftImg,_OBS_1[38]);
			gantt.setImageSrc(this.barCenterImg,_OBS_1[37]);
			gantt.setImageSrc(this.barRightImg,_OBS_1[36]);
			gantt.setBackgroundImage(this.barCenterDiv,_OBS_1[35]);
			
			_OBS_35(this,_OBS_1[40],[this.startDragLeft=this.scrollLeft]);
			return;
		}
		this.scrollTo(this.startDragLeft+(lp[0]-sp[0])/this.width*(this.scrollWidth-this.offsetWidth));
		if(type=="end")
		{
			gantt.setImageSrc(this.barLeftImg,_OBS_1[44]);
			gantt.setImageSrc(this.barCenterImg,_OBS_1[41]);
			gantt.setImageSrc(this.barRightImg,_OBS_1[43]);
			gantt.setBackgroundImage(this.barCenterDiv,_OBS_1[42]);
			
			_OBS_35(this,_OBS_1[39],[this.scrollLeft]);
		}
	},remove: function(e)
	{
		delete this.leftImg;
		delete this.rightImg;
		delete this.barDiv;
		delete this.barLeftImg;
		delete this.barRightImg;
		delete this.barCenterDiv;
		delete this.barCenterImg;
		SFGanttControl.prototype.remove.apply(this,arguments);
	}});
	 function SFGanttDivScroller(targetDiv)
	{
		this.layoutName="listFoot";
	}
	SFGanttDivScroller.prototype=new SFGanttControl()
	_OBS_4(SFGanttDivScroller.prototype,{remove: function(e)
	{
		delete this.leftImg;
		delete this.rightImg;
		delete this.barDiv;
		delete this.barLeftImg;
		delete this.barRightImg;
		delete this.barCenterDiv;
		delete this.barCenterImg;
		SFGanttControl.prototype.remove.apply(this,arguments);
	},onBarMove: function(sp,lp,type)
	{
		var gantt=this.gantt;
		if(type==_OBS_1[169])
		{
			gantt.setImageSrc(this.barLeftImg,_OBS_1[38]);
			gantt.setImageSrc(this.barCenterImg,_OBS_1[37]);
			gantt.setImageSrc(this.barRightImg,_OBS_1[36]);
			gantt.setBackgroundImage(this.barCenterDiv,_OBS_1[35]);
			
			_OBS_35(this,_OBS_1[40],[this.startDragLeft=this.scrollLeft]);
			return;
		}
		this.scrollTo(this.startDragLeft+(lp[0]-sp[0])/this.width*(this.scrollWidth-this.offsetWidth));
		if(type=="end")
		{
			gantt.setImageSrc(this.barLeftImg,_OBS_1[44]);
			gantt.setImageSrc(this.barCenterImg,_OBS_1[41]);
			gantt.setImageSrc(this.barRightImg,_OBS_1[43]);
			gantt.setBackgroundImage(this.barCenterDiv,_OBS_1[42]);
			
			_OBS_35(this,_OBS_1[39],[this.scrollLeft]);
		}
	},onSpacePress: function()
	{
		if(!this.spaceObj){return;}
		var spaceObj=this.spaceObj,toLeft=spaceObj.toLeft,point=this.scrollLeft;
		var offset=spaceObj.toLeft-this.scrollLeft;
		if(Math.abs(offset)<64){this.onMouseUp();return;}
		this.scrollTo(this.scrollLeft+(offset>0?64:-64));
	},onButtonPress: function()
	{
		if(!this.pressObj){return;}
		this.scrollTo(this.scrollLeft+this.pressObj.dir*8);
	},onMouseUp: function(e)
	{
		_OBS_26(e);
		if(e && e.target && e.target.ownerDocument.releaseCapture){e.target.ownerDocument.releaseCapture();}
		var gantt=this.gantt;
		if(this.pressObj)
		{
			var pressObj=this.pressObj;
			_OBS_35(this,_OBS_1[39],[this.scrollLeft]);
			window.clearInterval(pressObj.timeout);
			_OBS_32(pressObj.ul);
			gantt.setImageSrc(this.leftImg,_OBS_1[46]);
			gantt.setImageSrc(this.rightImg,_OBS_1[45]);
			this.pressObj=null;
		}
		if(this.spaceObj)
		{
			var spaceObj=this.spaceObj;
			_OBS_35(this,_OBS_1[39],[this.scrollLeft]);
			this.scrollTo(spaceObj.toLeft);
			window.clearInterval(spaceObj.timeout);
			_OBS_32(spaceObj.ul);
			this.spaceObj=null;
		}
	},onMouseDown: function(e)
	{
		_OBS_26(e);
		if(this.pressObj || this.spaceObj){this.onMouseUp(e);}
		var gantt=this.gantt,div=this.div,doc=div.ownerDocument;
		if(div.setCapture){div.setCapture();}
		switch(e.target)
		{
			case this.leftImg:
			case this.rightImg:
				var flag=(this.rightImg==e.target);
				gantt.setImageSrc(e.target,"scroll_"+(flag?_OBS_1[59]:_OBS_1[95])+'1');
				var pressObj=this.pressObj={
					dir:(flag?1:-1),
					timeout:window.setInterval(_OBS_22(this,this.onButtonPress),32),
					ul:_OBS_28(doc,_OBS_1[170],this,this.onMouseUp)
				};
				_OBS_35(this,_OBS_1[40],[this.scrollLeft]);
				this.onButtonPress();
				break;
			case this.div:
				var point=_OBS_37(e,this.div);
				var toLeft=point[0]/(this.size[0]-this.size[1]*2)*(this.scrollWidth-this.offsetWidth);
				var spaceObj=this.spaceObj={
					toLeft:toLeft,
					timeout:window.setInterval(_OBS_22(this,this.onSpacePress),128),
					ul:_OBS_28(doc,_OBS_1[170],this,this.onMouseUp)
				};
				_OBS_35(this,_OBS_1[40],[this.scrollLeft]);
				this.onSpacePress();
				break;
			default:
				new SFDragObject(div,_OBS_22(this,this.onBarMove),{interval:32}).onMouseDown(e)
				break;
		}
	},scrollTo: function(scrollLeft,trigger)
	{
		scrollLeft=this.scrollLeft=Math.max(Math.min(scrollLeft,this.scrollWidth-this.offsetWidth),0);
		_OBS_4(this.barDiv.style,{left:(this.size[1]+scrollLeft/(this.scrollWidth-this.offsetWidth)*this.width)+"px"});
		if(trigger!=false)
		{
			
			_OBS_35(this,_OBS_1[91],[scrollLeft]);
		}
	},init: function(offsetWidth,scrollWidth,scrollLeft)
	{
		if(!offsetWidth || !scrollWidth){return;}
		var width=this.size[0]-this.size[1]*2;
		this.offsetWidth=offsetWidth;
		this.scrollWidth=scrollWidth;
		this.barDiv.style.display=offsetWidth<scrollWidth?"":_OBS_1[164]
		var bWidth=Math.max(scrollWidth?parseInt(width*offsetWidth/scrollWidth):0,14);
		_OBS_4(this.barDiv.style,{width:bWidth+"px"});
		_OBS_4(this.barCenterDiv.style,{width:bWidth-6+"px"});
		this.width=width-bWidth;
		this.scrollTo(scrollLeft?scrollLeft:this.scrollLeft,false);
	},initialize: function(gantt)
	{
		if(!gantt.getLayout){return false;}
		var targetDiv=this.targetDiv=gantt.getLayout("listHead");
		if(!targetDiv){return false;}
		if(!SFGanttScrollerControl.prototype.initialize.apply(this,arguments)){return false;}
		this.startLeft=parseInt(targetDiv.firstChild.style.left);
		this.listeners.push(_OBS_28(this,_OBS_1[91],this,this.onScroll));
		this.listeners.push(_OBS_28(gantt,_OBS_1[69],this,this.onResize));
		return true;
	},onResize: function()
	{
		if(!this.container){return;}
		SFGanttScrollerControl.prototype.onResize.apply(this,arguments);
		this.init(this.targetDiv.offsetWidth,this.targetDiv.scrollWidth+this.startLeft);
	},onScroll: function(scrollLeft)
	{
		for(var child=this.targetDiv.firstChild;child;child=child.nextSibling)
		{
			if(!child.style){continue;}
			child.style.left=-scrollLeft+this.startLeft+"px";
		}
		_OBS_35(this.targetDiv,_OBS_1[91]);
	}});
	 function SFGanttTimeScroller()
	{
		this.layoutName="mapFoot";
	}
	SFGanttTimeScroller.prototype=new SFGanttScrollerControl()
	_OBS_4(SFGanttTimeScroller.prototype,{initialize: function(gantt)
	{
		if(!SFGanttScrollerControl.prototype.initialize.apply(this,arguments)){return false;}
		this.listeners.push(_OBS_28(this,_OBS_1[40],this,this.onScrollStart));
		this.listeners.push(_OBS_28(this,_OBS_1[91],this,this.onScroll));
		this.listeners.push(_OBS_28(this,_OBS_1[39],this,this.onScrollEnd));
		return true;
	},onResize: function()
	{
		if(!this.container){return;}
		SFGanttScrollerControl.prototype.onResize.apply(this,arguments);
		var width=this.gantt.getLayout(_OBS_1[85]).offsetWidth;
		this.init(width,width*9,width*4);
	},onScrollStart: function(scrollLeft)
	{
		this.scrollObj={start:scrollLeft,startTime:this.gantt.startTime};
	},onScroll: function(scrollLeft)
	{
		this.gantt.move(scrollLeft-this.scrollObj.start);
		this.scrollObj.start=scrollLeft;
	},onScrollEnd: function(e)
	{
		this.onResize();
	}});
	 function SFGanttSelectTaskOperateControl()
	{
	}
	SFGanttSelectTaskOperateControl.prototype=new SFGanttControl()
	_OBS_4(SFGanttSelectTaskOperateControl.prototype,{initialize: function(gantt,container)
	{
		if(!gantt.getSelectedElements){return false;}
		this.gantt=gantt;
		_OBS_4(gantt,{
			addTask:_OBS_22(this,this.addTask),
			deleteTask:_OBS_22(this,this.deleteTask),
			upgradeSelectedTasks:_OBS_22(this,this.upgradeSelectedTasks),
			degradeSelectedTasks:_OBS_22(this,this.degradeSelectedTasks),
			upgradeTask:_OBS_22(this,this.upgradeTask),
			degradeTask:_OBS_22(this,this.degradeTask),
			addTasksLinks:_OBS_22(this,this.addTasksLinks),
			removeTasksLinks:_OBS_22(this,this.removeTasksLinks),
			focusIntoView:_OBS_22(this,this.focusIntoView)
		});
		return true;
	},addTask: function()
	{
		var gantt=this.gantt,data=gantt.data,selectedTasks=gantt.getSelectedElements();
		var parent,pTask=null,beforeTask=selectedTasks[0]?selectedTasks[selectedTasks.length-1]:null;
		if(beforeTask)
		{
			if(!beforeTask.getPreviousSibling())
			{
				parent=beforeTask.getParent();
			}
			else
			{
				pTask=beforeTask.getPreviousSibling().getLastDescendant(true);
				parent=pTask.getParent();
			}
		}
		else
		{
			if(!data.getRootTask().getFirstChild())
			{
				parent=data.getRootTask();
			}
			else
			{
				pTask=data.getRootTask().getLastChild().getLastDescendant(true);
				parent=pTask.getParent();
			}
		}
		var newTask=data.addTask(parent,pTask);
		if(newTask)
		{
		    gantt.setSelectedElement(newTask);
		}
	},checkReadOnly: function()
	{
		var selectedTasks=this.gantt.getSelectedElements();
		var len=selectedTasks.length;
		for(var i=0;i<len;i++)
		{
			if(selectedTasks[i].ReadOnly)
			{
				var notice=this.gantt.config.getConfig("SFGantt/noticeReadonly");
				if(notice){alert(notice);}
				return false;
			}
		}
		return true;
	},deleteTask: function()
	{
		if(!this.checkReadOnly()){return false;}
		var selectedTasks=this.gantt.getSelectedElements();
		var len=selectedTasks.length;
		if(!selectedTasks[0]){return;}
		if(len==0){return;}
		var notice=this.gantt.config.getConfig("SFGantt/noticeDelete");
		if(notice && !window.confirm(notice)){return}
		for(var i=selectedTasks.length-1;i>=0;i--)
		{
			var task=selectedTasks[i];
			if(!task){continue;}
			this.gantt.data.deleteTask(task);
		}
	},getTopSelectedTasks: function()
	{
		var tasks=[],selectedTasks=this.gantt.getSelectedElements();
		for(var i=0;i<selectedTasks.length;i++)
		{
			var j;
			for(var j=tasks.length-1;j>=0;j--)
			{
				if(selectedTasks[i].contains(tasks[j]))
				{
					tasks[j]=selectedTasks[i];
					break;
				}
				else if(tasks[j].contains(selectedTasks[i]))
				{
					break;
				}
			}
			if(j<0)
			{
				tasks.push(selectedTasks[i]);
			}
		}
		return tasks;
	},upgradeSelectedTasks: function()
	{
		if(!this.checkReadOnly()){return false;}
		var tasks=this.getTopSelectedTasks();
		for(var i=0;i<tasks.length;i++)
		{
			this.upgradeTask(tasks[i]);
		}
	},degradeSelectedTasks: function()
	{
		if(!this.checkReadOnly()){return false;}
		var tasks=this.getTopSelectedTasks();
		for(var i=0;i<tasks.length;i++)
		{
			this.degradeTask(tasks[i]);
		}
	},upgradeTask: function(task)
	{
		var data=this.gantt.data,parent=task.getParent();
		if(!parent || parent==data.getRootTask()){return false;}
		var nTask=task.getNextSibling(),result=true;
		if(!data.moveTask(task,parent.getParent(),parent)){return false;}
		while (nTask)
		{
			var nnTask=nTask.getNextSibling()
			if(!data.moveTask(nTask,task,task.getLastChild())){return false;}
			nTask=nnTask
		}
		return true;
	},degradeTask: function(task)
	{
		var pTask=task.getPreviousSibling();
		if(!pTask){return false;}
		return this.gantt.data.moveTask(task,pTask,pTask.getLastChild())
	},addTasksLinks: function()
	{
		var selectedTasks=this.gantt.getSelectedElements();
		if(selectedTasks.length<2){return false;}
		for(var i=1;i<selectedTasks.length;i++)
		{
			selectedTasks[i].addPredecessorLink(selectedTasks[i-1],1);
		}
		return true;
	},removeTasksLinks: function()
	{
		var gantt=this.gantt,data=gantt.data,selectedTasks=gantt.getSelectedElements();
		if(selectedTasks.length<2){return false;}
		for(var i=0;i<selectedTasks.length;i++)
		{
			for(var j=0;j<selectedTasks.length;j++)
			{
				if(i==j){continue;}
				var links=selectedTasks[i].getPredecessorLinks();
				for(var k=links.length-1;k>=0;k--)
				{
					if(links[k].PredecessorTask==selectedTasks[j])
					{
						data.deleteLink(links[k]);
						break;
					}
				}
			}
		}
		return true;
	},focusIntoView: function()
	{
		var gantt=this.gantt,task=gantt.getFocusElement();
		if(!task || !task.Start || !gantt.moveTo){return;}
		if(gantt.getViewIndex(task)<0){gantt.scrollToElement(task,50);}
		gantt.moveTo(task.Start);
		gantt.move(-10);
	},remove: function()
	{
		var gantt=this.gantt;
		delete gantt.addTask
		delete gantt.deleteTask
		delete gantt.upgradeSelectedTasks
		delete gantt.degradeSelectedTasks
		delete gantt.upgradeTask
		delete gantt.degradeTask
		delete gantt.addTasksLinks
		delete gantt.removeTasksLinks
		delete gantt.focusIntoView
		SFGanttControl.prototype.remove.apply(this,arguments);
	}});
	 function SFGanttSizeLimitControl()
	{
	}
	SFGanttSizeLimitControl.prototype=new SFGanttControl()
	_OBS_4(SFGanttSizeLimitControl.prototype,{initialize: function(gantt)
	{
		this.listeners=[
			_OBS_28(gantt,_OBS_1[93],this,this.onBeforeResize)
		]
		var maxSize=gantt.maxSize,minSize=gantt.minSize;
		maxSize=maxSize?maxSize:[1000,1000];
		minSize=minSize?minSize:[200,200];
		_OBS_4(this,{maxSize:maxSize,minSize:minSize,gantt:gantt});
		gantt.setMaxSize=_OBS_22(this,function(size){this.maxSize=size;});
		gantt.setMinSize=_OBS_22(this,function(size){this.minSize=size;});
		return true;
	},onBeforeResize: function(returnObj,s)
	{
		var size=this.maxSize;
		if(size && (size[0]<s[0] || size[1]<s[1])){returnObj.returnValue=false;}
		var size=this.minSize;
		if(size && (size[0]>s[0] || size[1]>s[1])){returnObj.returnValue=false;}
	},remove: function()
	{
		var gantt=this.gantt;
		delete gantt.setMaxSize
		delete gantt.setMinSize
		SFGanttControl.prototype.remove.apply(this,arguments);
	}});
	 function SFGanttTasksMap(config)
	{
		_OBS_53(this,config);
		this.items=[];
	}
	SFGanttTasksMap.prototype=new SFGanttControl()
	_OBS_4(SFGanttTasksMap.prototype,{initialize: function(gantt)
	{
		if(!gantt.getMapPanel){return false;}
		_OBS_53(this,gantt.config.getConfigObj("SFGanttTasksMap"));
		if(!SFGanttTasksMap.listIndex){SFGanttTasksMap.listIndex=0;}
		this.proTag="taskMap_"+(SFGanttTasksMap.listIndex++);
		_OBS_4(this,{
			gantt:gantt,
			taskHeight:12,					
			taskStyles:gantt.config.getConfigObj("SFGantt/taskStyle")
		});
		if(!this.taskStyles){this.taskStyles={};}
		this.taskPadding=gantt.itemHeight-this.taskHeight;
		
		var doc=gantt.container.ownerDocument,tasksDiv=this.div=doc.createElement("div");
		_OBS_4(this.div.style,{position:_OBS_1[178],fontSize:'0px',left:'0px',top:'-1px',zIndex:100});
		var firstDiv=doc.createElement("div");
		_OBS_4(firstDiv.style,{position:_OBS_1[178],padding:'0px',margin:'0px',border:'0px'});
		tasksDiv.appendChild(firstDiv);
		gantt.getMapPanel().appendChild(tasksDiv);
		this.listeners=[
			_OBS_28(gantt,_OBS_1[94],this,this.onInitialize),
			_OBS_28(gantt,_OBS_1[83],this,this.onScale),
			_OBS_28(gantt,_OBS_1[57],this,this.drawTask),
			_OBS_28(gantt,_OBS_1[56],this,this.clearTask),
			_OBS_28(gantt,_OBS_1[55],this,this.updateTask),
			_OBS_28(tasksDiv,_OBS_1[72],this,this.onDblClick),
			_OBS_28(tasksDiv,_OBS_1[73],this,this.onClick),
			_OBS_28(tasksDiv,_OBS_1[172],this,this.onMouseDown)
		];
		if(gantt.setTooltip)
		{
			gantt.setTooltip(tasksDiv,_OBS_22(this,this.getTooltip))
		}
		return true;
	},addItem: function(item)
	{
		if(!item){return;}
		if(!item.initialize(this)){return false;}
		this.items.push(item);
		return true;
	},setViewTop: function()
	{
		var top=this.gantt.getViewTop();
		this.div.firstChild.style.height=top+"px";
	},getTaskStyle: function(task)
	{
		var className=task.ClassName,taskStyles=this.taskStyles;
		className=className && taskStyles[className]?className:this.taskStyle;
		return taskStyles[className];
	},drawTask: function(task,viewIndex)
	{
		var gantt=this.gantt,scale=gantt.getScale();
		if(!scale){return;}
		if(viewIndex==0){this.setViewTop();}
		var drawObj=gantt.getElementDrawObj(task);
		var mapObj=drawObj[this.proTag]={};
		var start=task.Start,finish=task.Finish,height=gantt.getElementHeight(task);
		var taskDiv=this.div.ownerDocument.createElement("div"),childNodes=this.div.childNodes;
		taskDiv.style.cssText="position:relative;top:"+(height-gantt.getElementDrawObj(task).height)+"px;left:"+gantt.getMapPanelPosition(start)+_OBS_1[34]+height+"px"
		mapObj.taskDiv=taskDiv;

		if(drawObj.height>0)
		{
			taskDiv._element=task;
			var items=this.items;
			if(finish && start && finish>=start)
			{
				for(var i=items.length-1;i>=0;i--)
				{
					items[i].show(task,mapObj,scale);
				}
			}
		}

		if(viewIndex+1==childNodes.length)
		{
		    this.div.appendChild(taskDiv);
		}
		else
		{
		    this.div.insertBefore(taskDiv,childNodes[viewIndex+1]);
		}
	},updateTask: function(task,changedFields)
	{
		if(!this.gantt.getElementDrawObj(task)){return;}
		var drawObj=this.gantt.getElementDrawObj(task),mapObj=drawObj[this.proTag];
		if(!mapObj){return;}
		var start=task.Start,finish=task.Finish;
		mapObj.taskDiv.style.left=this.gantt.getMapPanelPosition(start)+"px";
		var items=this.items,canShow=(finish && start && finish>=start && drawObj.height>0);
		if(_OBS_13(changedFields,_OBS_1[65]))
		{
			mapObj.taskDiv.style.backgroundColor=task.Selected?_OBS_1[33]:"";
		}
		for(var i=items.length-1;i>=0;i--)
		{
			if(canShow)
			{
				items[i].onUpdate(task,mapObj,changedFields);
			}
			else
			{
				items[i].remove(task,mapObj);
			}
		}
	},clearTask: function(task,viewIndex)
	{
		if(viewIndex==0){this.setViewTop();}
		var drawObj=this.gantt.getElementDrawObj(task);
		if(!drawObj){return}
		var mapObj=drawObj[this.proTag];
		if(!mapObj){return}
		var items=this.items;
		for(var i=items.length-1;i>=0;i--)
		{
			items[i].remove(task,mapObj);
		}
		if(mapObj){mapObj.taskDiv._element=null;}
		_OBS_29(mapObj.taskDiv);
		drawObj[this.proTag]=null;
	},getEventElement: function(e)
	{
		if(!e.target){e.target=e.srcElement}
		for(var node=e.target;node;node=node.parentNode)
		{
			if(node==this.div){return null;}
			if(node._element){return node._element;}
		}
	},onDblClick: function(e)
	{
		var task=this.getEventElement(e);
		if(!task){return;}
		
		_OBS_35(this.gantt,"taskdblclick",[task,"chart"]);
	},onClick: function(e)
	{
		var task=this.getEventElement(e);
		if(!task){return;}
		
		_OBS_35(this.gantt,"taskclick",[task,e]);
	},onMouseDown: function(e)
	{
		if(_OBS_39(e)!=1){return;}
		var task=this.getEventElement(e);
		if(!task){return;}
		_OBS_35(this.gantt,"taskmousedown",[task,e]);
		this.dragTask=task;
		var mapObj=this.gantt.getElementDrawObj(task)[this.proTag];
		var items=this.items;
		for(var i=items.length-1;i>=0;i--)
		{
			items[i].onMouseDown(task,mapObj,e);
		}
	},onInitialize: function()
	{
		
		this.addItem(new SFGanttMapMilestoneHead());
		this.addItem(new SFGanttMapSummaryHead());
		this.addItem(new SFGanttMapBarSummary());
		this.addItem(new SFGanttMapBarNormal());
		this.addItem(new SFGanttMapText());
		this.addItem(new SFGanttMapResize());
		this.addItem(new SFGanttMapPercentChange());
		this.addItem(new SFGanttMapPercent());
		this.addItem(new SFGanttMapBarTrack());
		this.addItem(new SFGanttMapMilestoneTrackHead());
	
		var gantt=this.gantt;
		if(!gantt.getScale()){return;}
		
		var viewTasks=gantt.getViewElements();
		for(var i=0;i<viewTasks.length;i++)
		{
			this.drawTask(viewTasks[i],i);
		}
	},onScale: function()
	{
		
		var gantt=this.gantt,scale=gantt.getScale();
		if(!scale){return;}
		
		var viewTasks=gantt.getViewElements(),items=this.items;
		for(var i=0;i<viewTasks.length;i++)
		{
			var task=viewTasks[i],mapObj=this.gantt.getElementDrawObj(task)[this.proTag];
			if(!mapObj){continue;}
			var start=task.Start;
			mapObj.taskDiv.style.left=gantt.getMapPanelPosition(start)+"px";
			for(var j=items.length-1;j>=0;j--)
			{
				items[j].onScale(task,mapObj,scale);
			}
		}
	},getTooltip: function(tooltip,e)
	{
		var task=this.getEventElement(e);
		if(!task){return;}
		var items=this.items,mapObj=this.gantt.getElementDrawObj(task)[this.proTag];
		for(var i=items.length-1;i>=0;i--)
		{
			if(items[i].getTooltip(task,mapObj,tooltip,e)){return true;}
		}
		return false;
	},getTaskTooltipContent: function(task,title,fields)
	{
		var doc=this.div.ownerDocument,table=doc.createElement(_OBS_1[75]);
		table.style.fontSize="12px";
		_OBS_4(table,{});
		var row=table.insertRow(-1);
		var cell=row.insertCell(-1);
		_OBS_4(cell,{align:_OBS_1[81],colSpan:2,noWrap:true});
		cell.appendChild(doc.createTextNode(title));

		fields=_OBS_86(fields);
		for(var i=0;i<fields.length;i++)
		{
			var field=fields[i];
			row=table.insertRow(-1);
			cell=row.insertCell(-1);
			_OBS_4(cell,{align:_OBS_1[95],noWrap:true});
			field.showHead(cell);
			cell=row.insertCell(-1);
			_OBS_4(cell,{align:_OBS_1[95],noWrap:true});
			field.showBody(cell,task,this);
		}
		return table;
	},getLinkTooltipContent: function(link)
	{
		var doc=this.div.ownerDocument,table=doc.createElement(_OBS_1[75]);
		table.style.fontSize="12px";
		_OBS_4(table,{});
		var row=table.insertRow(-1);
		var cell=row.insertCell(-1);
		_OBS_4(cell,{align:_OBS_1[81],colSpan:2,noWrap:true});
		var title=this.tooltipTitle['link'];
		cell.appendChild(doc.createTextNode(title));

		var fields=_OBS_84(_OBS_1[158],this.linkAddNoticeFields.split(","));

		for(var i=0;i<fields.length;i++)
		{
			var field=fields[i];
			row=table.insertRow(-1);
			cell=row.insertCell(-1);
			_OBS_4(cell,{align:_OBS_1[95],noWrap:true});
			field.showHead(cell);
			cell=row.insertCell(-1);
			_OBS_4(cell,{align:_OBS_1[95],noWrap:true});
			field.showBody(cell,link,this);
		}
		return table;
	}});
	 function SFGanttTimeControl()
	{
	}
	SFGanttTimeControl.prototype=new SFGanttControl()
	_OBS_4(SFGanttTimeControl.prototype,{initialize: function(gantt,container)
	{
		this.gantt=gantt;
		_OBS_4(gantt,{
			getStartTime:_OBS_22(this,this.getStartTime),
			getScale:_OBS_22(this,this.getScale),
			setStartTime:_OBS_22(this,this.setStartTime),
			setScale:_OBS_22(this,this.setScale),
			move:_OBS_22(this,this.move),
			show:_OBS_22(this,this.show)
		});
		gantt.moveTo=gantt.setStartTime
		this.listeners=[
			_OBS_28(gantt,_OBS_1[94],this,this.onGanttInit)
		];
		return true;
	},onGanttInit: function()
	{
		var gantt=this.gantt;
		this.startTime=gantt.startTime;
		if(!this.startTime)
		{
			var task=gantt.data.getRootTask();
			if(task){this.startTime=task.Start;}
			if(!this.startTime){this.startTime=new Date();}
		}
		if(!this.scale){this.scale=576*3600000/12;}
	},move: function(length)
	{
		this.setStartTime(new Date(length*this.scale+this.startTime.valueOf()));
	},getStartTime: function()
	{
		return this.startTime;
	},setStartTime: function(time)
	{
		var gantt=this.gantt,startTime=this.startTime;
		if(startTime && (startTime==time || startTime.valueOf()==time.valueOf())){return;}
		var returnObj={returnValue:true}
		
		_OBS_35(gantt,"beforestarttimechange",[returnObj,time]);
		if(!returnObj.returnValue){return false;}
		this.startTime=time;
		
		
		_OBS_35(gantt,_OBS_1[32],[time]);
		_OBS_35(gantt,_OBS_1[144],[time]);
		return true;
	},getScale: function()
	{
		return this.scale;
	},setScale: function(scale)
	{
		if(this.scale==scale){return;}
		var returnObj={returnValue:true}
		
		_OBS_35(this.gantt,"beforescalechange",[returnObj,scale]);
		if(!returnObj.returnValue){return false;}
		this.scale=scale;
		
		_OBS_35(this.gantt,_OBS_1[83],[scale]);
		return true;
	},show: function(startTime,scale)
	{
		var gantt=this.gantt;
		if(startTime){gantt.setStartTime(startTime);}
		if(scale){gantt.setScale(scale);}
	},remove: function()
	{
		var gantt=this.gantt;
		delete gantt.moveTo
		delete gantt.getStartTime
		delete gantt.getScale
		delete gantt.setStartTime
		delete gantt.setScale
		delete gantt.move
		delete gantt.show
		SFGanttControl.prototype.remove.apply(this,arguments);
	}});
	 function SFGanttMapPanel()
	{
	}
	SFGanttMapPanel.prototype=new SFGanttControl()
	_OBS_4(SFGanttMapPanel.prototype,{initialize: function(gantt)
	{
		if(!gantt.getLayout || !gantt.getStartTime || !gantt.getLayout(_OBS_1[85])){return false;}
		var container=this.div=gantt.container.ownerDocument.createElement("div");
		_OBS_4(container.style,{position:_OBS_1[178],left:'0px',top:'0px'});
		gantt.getLayout(_OBS_1[85]).appendChild(container);
		if(!container){return false;}
		this.gantt=gantt;
		gantt.getMapPanel=_OBS_22(this,this.getMapPanel)
		gantt.getMapPanelPosition=_OBS_22(this,this.getMapPanelPosition);
		gantt.getTimeByMapPanelPosition=_OBS_22(this,this.getTimeByMapPanelPosition);
		this.listeners=[
			_OBS_28(gantt,_OBS_1[94],this,this.onGanttInit),
			_OBS_28(gantt,_OBS_1[32],this,this.onTimeChange),
			_OBS_28(gantt,_OBS_1[83],this,this.onTimeChange)
		];
		if(!gantt.disableMapDrag)
		{
			this.listeners=this.listeners.concat(_OBS_59(container,_OBS_22(this,this.onMove),{container:gantt.getContainer()}));
		}
		return true;
	},onGanttInit: function()
	{
		this.drawStart=this.gantt.getStartTime();
		this.onTimeChange();
	},onTimeChange: function(time)
	{
		this.div.style.left=-Math.round(this.gantt.getStartTime()-this.drawStart)/this.gantt.getScale()+"px";
	},getMapPanelPosition: function(time)
	{
		if(!time){return 0;}
		return Math.round(time-this.drawStart)/this.gantt.getScale();
	},getTimeByMapPanelPosition: function(position)
	{
		position=position?position:0;
		return new Date(position*this.gantt.getScale()+this.drawStart.valueOf());
	},getMapPanel: function()
	{
		return this.div;
	},onMove: function(sp,lp,type)
	{
		var gantt=this.gantt,scrollDiv=gantt.getLayout(_OBS_1[47]);
		if(type==_OBS_1[169])
		{
			this.startPosition=scrollDiv.scrollTop;
			this.startTime=gantt.getStartTime();
		}
		var scrollTop=scrollDiv.scrollTop=this.startPosition-lp[1]+sp[1];
		
		_OBS_35(gantt,_OBS_1[91],[scrollTop]);
		gantt.setStartTime(new Date(this.startTime.valueOf()+(sp[0]-lp[0])*gantt.getScale()));
	},remove: function()
	{
		var gantt=this.gantt;
		delete gantt.getMapPanel;
		delete gantt.getMapPanelPosition;
		delete getTimeByMapPanelPosition;
		SFGanttControl.prototype.remove.apply(this,arguments);
	}});
	 function SFGanttTimePanel()
	{
	}
	SFGanttTimePanel.prototype=new SFGanttControl()
	_OBS_4(SFGanttTimePanel.prototype,{initialize: function(gantt)
	{
		if(!gantt.getLayout || !gantt.getStartTime || !gantt.getLayout(_OBS_1[85])){return false;}
		var container=this.div=gantt.container.ownerDocument.createElement("div");
		_OBS_4(container.style,{position:_OBS_1[179],left:'0px',top:'0px',width:_OBS_1[177],height:_OBS_1[177],zIndex:10});
		gantt.getContainer().appendChild(container);
		if(!container){return false;}
		this.gantt=gantt;
		gantt.getTimePanel=_OBS_22(this,this.getTimePanel)
		gantt.getTimePanelPosition=_OBS_22(this,this.getTimePanelPosition);
		this.listeners=[
			_OBS_28(gantt,_OBS_1[94],this,this.onGanttInit),
			_OBS_28(gantt,_OBS_1[84],this,this.onTimeChange),
			_OBS_28(gantt,_OBS_1[32],this,this.onTimeChange),
			_OBS_28(gantt,_OBS_1[83],this,this.onTimeChange)
		];
		return true;
	},onGanttInit: function()
	{
		this.drawStart=this.gantt.getStartTime();
		this.onTimeChange();
	},onTimeChange: function(time)
	{
		if(!this.drawStart){return;}
		var gantt=this.gantt;
		this.div.style.left=-Math.round((gantt.getStartTime()-this.drawStart)/gantt.getScale()-_OBS_36(gantt.getLayout(_OBS_1[85]),gantt.getContainer())[0])+"px";
	},getTimePanelPosition: function(time)
	{
		if(!time){return 0;}
		return Math.round(time-this.drawStart)/this.gantt.getScale();
	},getTimePanel: function()
	{
		return this.div;
	},remove: function()
	{
		var gantt=this.gantt;
		delete gantt.getTimePanel
		delete gantt.getTimePanelPosition
		SFGanttControl.prototype.remove.apply(this,arguments);
	}});
	 function SFGanttTimeLine(time,dragable,style)
	{
		_OBS_4(this,{time:time,dragable:dragable,style:style});
	}
	SFGanttTimeLine.prototype=new SFGanttControl()
	_OBS_4(SFGanttTimeLine.prototype,{initialize: function(gantt)
	{
		if(!gantt.getTimePanel){return false;}
		var container=gantt.getTimePanel()
		if(!container){return false;}
		gantt.addTimeLine=_OBS_22(gantt,_OBS_82);
		if(!this.time){return false;}
		this.gantt=gantt;
		_OBS_53(this,gantt.config.getConfigObj("SFGanttTimeLine"));
		
		var div=this.div=gantt.container.ownerDocument.createElement("div");
		_OBS_4(div.style,this.lineStyle);
		_OBS_4(div.style,this.style);
		_OBS_4(div.style,{position:_OBS_1[179],fontSize:'0px',left:'-1px',top:'0px',height:_OBS_1[177],zIndex:200});
		container.appendChild(div);
		this.listeners=[
			_OBS_28(gantt,_OBS_1[83],this,this.onMove)
		];
		if(this.dragable)
		{
			_OBS_11(div,_OBS_1[79]);
			this.listeners.push(_OBS_59(div,_OBS_22(this,this.onDrag),{container:container}));
		}
		this.onMove();
		return true;
	},onMove: function()
	{
		var gantt=this.gantt,scale=gantt.getScale(),startTime=gantt.getStartTime();
		if(!scale || !startTime){return;}
		this.div.style.left=gantt.getTimePanelPosition(this.time)+"px";
	},moveTo: function(time)
	{
		this.time=time;
		this.onMove();
	},onDrag: function(sp,lp,type)
	{
		if(type==_OBS_1[169]){this.dragStart=this.time.valueOf();}
		var gantt=this.gantt,time=new Date(this.dragStart+(lp[0]-sp[0])*this.gantt.getScale())
		this.moveTo(time);
		if(gantt.getTooltip)
		{
			var tooltip=gantt.getTooltip(),tpPosition=_OBS_36(gantt.getTimePanel(),gantt.getContainer());
			tooltip.setContent(this.div.ownerDocument.createTextNode(_OBS_9(time,this.tooltipFormat)));
			tooltip.show([lp[0]+tpPosition[0],lp[1]+tpPosition[1]]);
		}
	}});
	 function _OBS_82(time,dragable,style)
	{
		var line=new SFGanttTimeLine(time,dragable,style);
		this.addControl(line);
		return line;
	}
	_OBS_4(SFGanttTimeLine,{addTimeLine:_OBS_82});
	 function SFGanttTimeScrollNotice(gantt,container)
	{
	}
	SFGanttTimeScrollNotice.prototype=new SFGanttControl()
	_OBS_4(SFGanttTimeScrollNotice.prototype,{initialize: function(gantt,container)
	{
		if(gantt.disableTimeScrollNotice || !gantt.getLayout){return false;}
		_OBS_53(this,gantt.config.getConfigObj("SFGanttTimeScrollNotice"));
		this.gantt=gantt;
		
		this.div=container.ownerDocument.createElement("div");
		_OBS_4(this.div.style,{position:_OBS_1[179],zIndex:400,display:_OBS_1[164],left:"100px"});
		_OBS_4(this.div.style,this.divStyle);
		container.appendChild(this.div);
		this.listeners=[
			_OBS_28(gantt,_OBS_1[144],this,this.onMove),
			_OBS_28(gantt,_OBS_1[84],this,this.onResize)
		];
		return true;
	},onMove: function(time)
	{
		if(!this.timeout){this.timeout=window.setInterval(_OBS_22(this,this.onTime),64);}
		this.lastTime=time;
		this.idleTimes=0
		this.changed=true;
	},onTime: function()
	{
		if(!this.changed)
		{
			this.idleTimes++;
			if(this.idleTimes>4)
			{
				window.clearInterval(this.timeout);
				this.div.style.display=_OBS_1[164];
				delete this.timeout
			}
			return;
		}
		this.changed=false;
		this.div.style.display="";
		this.div.innerHTML=_OBS_9(this.lastTime,this.dateFormat);
	},onResize: function()
	{
		var mapDiv=this.gantt.getLayout(_OBS_1[85]);
		this.div.style.left=(_OBS_36(mapDiv,this.gantt.getContainer())[0]+1)+"px"
		this.div.style.bottom=this.gantt.footHeight+5+"px"
	}});
	 function SFGanttTimeSegmentation()
	{
	}
	SFGanttTimeSegmentation.prototype=new SFGanttControl()
	_OBS_4(SFGanttTimeSegmentation.prototype,{initialize: function(gantt)
	{
		if(gantt.disableTimeSegmentation || !gantt.getTimePanel || !gantt.getCalList){return false;}
		var container=gantt.getTimePanel();
		if(!container){return false;}
		_OBS_53(this,gantt.config.getConfigObj("SFGanttTimeSegmentation"));
		this.gantt=gantt;
		
		var div=this.div=gantt.container.ownerDocument.createElement("div");
		_OBS_4(div.style,{position:_OBS_1[179],fontSize:'0px',left:'0px',top:'0px',height:_OBS_1[177],width:_OBS_1[177],zIndex:20});
		container.appendChild(div);
		this.listeners=[
			_OBS_28(gantt,_OBS_1[94],this,this.reDraw),
			_OBS_28(gantt,_OBS_1[84],this,this.reDraw),
			_OBS_28(gantt,_OBS_1[83],this,this.reDraw),
			_OBS_28(gantt,_OBS_1[144],this,this.reDraw)
		];
		this.reDraw();
		return true;
	},reDraw: function()
	{
		var gantt=this.gantt,cals=gantt.getCalList();
		if(!cals ||!cals[1]){return;}
		this.showSegmentations(gantt.getScale(),gantt.getStartTime().valueOf(),cals[1]);
	},showSegmentations: function(scale,startTime,cal)
	{
		if(this.cal!=cal || !this.drawStart || Math.abs(startTime-this.drawStart)/scale>10000)
		{
			this.clear();
			_OBS_4(this,{cal:cal,drawStart:startTime,scale:scale});
			this.div.style.left=this.gantt.getTimePanelPosition(startTime)+"px";
		}
		if(this.scale!=scale)
		{
			for(var child=this.div.firstChild;child;child=child.nextSibling)
			{
				child.style.left=(child.sTime-this.drawStart)/scale+1+"px";
			}
			this.div.style.left=this.gantt.getTimePanelPosition(this.drawStart)+"px";
			this.scale=scale;
		}
		var endTime=startTime+this.div.offsetWidth*scale;
		var calDiv=this.div;
		var osTime=calDiv.firstChild?calDiv.firstChild.sTime:Number.MAX_VALUE;
		var oeTime=calDiv.lastChild?calDiv.lastChild.eTime:Number.MIN_VALUE;
		if(startTime>(calDiv.firstChild?calDiv.firstChild.eTime:Number.MAX_VALUE))
		{
			while(calDiv.firstChild && calDiv.firstChild.eTime<startTime)
			{
				_OBS_29(calDiv.firstChild);
			}
			osTime=calDiv.firstChild?calDiv.firstChild.sTime:Number.MAX_VALUE
		}
		if((calDiv.lastChild?calDiv.lastChild.sTime:Number.MIN_VALUE)>endTime)
		{
			while(calDiv.lastChild && calDiv.lastChild.sTime>endTime)
			{
				_OBS_29(calDiv.lastChild);
			}
			oeTime=calDiv.lastChild?calDiv.lastChild.eTime:Number.MIN_VALUE
		}
		if(startTime<osTime)
		{
			this.addSegmentation(startTime,Math.min(osTime,endTime),cal,calDiv,scale,-1);
			osTime=calDiv.firstChild?calDiv.firstChild.sTime:Number.MAX_VALUE;
			oeTime=calDiv.lastChild?calDiv.lastChild.eTime:Number.MIN_VALUE;
		}
		if(oeTime<endTime)
		{
			this.addSegmentation(Math.max(oeTime,startTime),endTime,cal,calDiv,scale,1);
		}
	},addSegmentation: function(startTime,endTime,cal,calDiv,scale,position)
	{
		var sTime=parseInt(cal.getFloorTime(new Date(startTime)).valueOf());
		var lastAdd=null;
		while(sTime<endTime)
		{
			var eTime=parseInt(cal.getNextTime(new Date(sTime)).valueOf());
			var div=this.div.ownerDocument.createElement("div");
			_OBS_4(div,{sTime:sTime,eTime:eTime});
			_OBS_4(div.style,{position:_OBS_1[179],left:(sTime-this.drawStart)/scale+1+"px",top:'0px',width:'0px',height:_OBS_1[177],borderLeft:_OBS_1[31]});
			_OBS_4(div.style,this.lineStyle);
			if(position==-1)
			{
				if(lastAdd==null)
				{
					calDiv.insertBefore(div,calDiv.firstChild);
				}
				else if(lastAdd.nextSibling==null)
				{
					calDiv.appendChild(div);
				}
				else
				{
					calDiv.insertBefore(div,lastAdd.nextSibling);
				}
			}
			else
			{
				calDiv.appendChild(div);
			}
			lastAdd=div;
			sTime=eTime;
		}
	},clear: function()
	{
		_OBS_29(this.div,true);
	}});
	 function SFGanttTooltipControl()
	{
	}
	SFGanttTooltipControl.prototype=new SFGanttControl()
	_OBS_4(SFGanttTooltipControl.prototype,{initialize: function(gantt)
	{
		if(gantt.disableTooltip){return false;}
		_OBS_53(this,gantt.config.getConfigObj("SFTooltip"));
		var div=gantt.container.ownerDocument.createElement("div");
		_OBS_4(div.style,{position:_OBS_1[179],zIndex:650});
		_OBS_4(div.style,this.divStyle);
		var container=gantt.container;
		_OBS_4(this,{gantt:gantt,div:div,container:container});
		this.setEnable(true);
		_OBS_4(gantt,{
			getTooltip:_OBS_22(this,this.getTooltip),
			setTooltip:_OBS_22(this,this.setTooltip)
		});
		return true;
	},onMouseOver: function(e)
	{
		var target=e.target;
		while(target)
		{
			if(target._SF_E_ && target._SF_E_ .tooltip && target._SF_E_ .tooltip(this,e))
			{
				_OBS_26(e);
				this.show(_OBS_38(e,this.container),target);
				return;
			}
			target=target.parentNode;
		}
	},setEnable: function(enable)
	{
		if(enable && !this.listeners)
		{
			this.listeners=[
				_OBS_28(this.container,"mouseover",this,this.onMouseOver)
			];
		}
		else if(!enable && this.listeners)
		{
			_OBS_32(this.listeners[0]);
			delete this.listeners;
		}
	},setContent: function(content)
	{
		_OBS_29(this.div,true);
		this.div.appendChild(content);
	},getContent: function()
	{
		return this.div;
	},show: function(position,div)
	{
		div=div?div:this.div;
		this.container.appendChild(this.div);
		var left=position[0]+5,top=position[1]+5;
		if(!this.position)
		{
			if(left+this.div.offsetWidth>this.container.offsetWidth){left=position[0]-this.div.offsetWidth-2;}
			if(top+this.div.offsetHeight>this.container.offsetHeight){top=position[1]-this.div.offsetHeight-2;}
		}
		_OBS_4(this.div.style,{left:left+"px",top:top+"px"});
		this.container._ganttTip=this;
		this.hl=_OBS_28(div,"mouseout",this,function(e)
		{
			if(!this.listeners){return;}
			this.hidden();
		})
	},hidden: function()
	{
		if(this.div.parentNode==this.container)
		{
			this.container.removeChild(this.div);
		}
		this.container._ganttTip=null;
		this.bindObject=null;
		_OBS_32(this.hl);
	},setTooltip: function(div,handle)
	{
		if(!div._SF_E_){div._SF_E_=[];}
		div._SF_E_.tooltip=handle;
	},getTooltip: function()
	{
		return this;
	},remove: function()
	{
		this.setEnable(false);
		this.hidden();
		var gantt=this.gantt;
		delete gantt.getTooltip
		delete gantt.setTooltip
		delete this.container
		SFGanttControl.prototype.remove.apply(this,arguments);
	}});
	 function SFGanttDrawControl()
	{
	}
	SFGanttDrawControl.prototype=new SFGanttControl()
	_OBS_4(SFGanttDrawControl.prototype,{initialize: function(gantt)
	{
		this.gantt=gantt;
		
		this.itemHeight=gantt.itemHeight;
		this.inline=gantt.inline;
		this.hideSummary=gantt.hideSummary;
		gantt.getElementDrawObj=_OBS_22(this,this.getElementDrawObj);
		gantt.removeElementDrawObj=_OBS_22(this,this.removeElementDrawObj);
		gantt.getElementHeight=_OBS_22(this,this.getElementHeight);
		this.listeners=[_OBS_28(gantt,gantt.elementType.toLowerCase()+_OBS_1[141],this,this.onElementChange)]
		return true;
	},getElementDrawObj: function(element)
	{
		var tagName=this.getTagName();
		if(!element[tagName])
		{
			
			var _height=this.getElementHeight(element),height=(this.hideSummary && element.Summary)?0:(this.inline?this.itemHeight:_height);
			element[tagName]={height:height,_height:_height};
		}
		return element[tagName];
	},removeElementDrawObj: function(element)
	{
		var tagName=this.getTagName();
		delete element[tagName];
	},getTagName: function()
	{
		if(!this.tagName)
		{
			if(!SFGantt._tagIndex){SFGantt._tagIndex=0;}
			this.tagName="drawObj_"+(SFGantt._tagIndex++);
		}
		return this.tagName;
	},getElementHeight: function(element)
	{
		var itemHeight,pElement;
		if(element.Summary && this.hideSummary){return 0;}
		if(this.inline)
		{
			if(!element.Summary && element.Start && element.Finish && (pElement=element.getPreviousSibling()) && !pElement.Summary && pElement.Start && pElement.Finish && pElement.Finish<element.Start)
			{
				var returnObj={returnValue:true};
				_OBS_35(this.gantt,"beforeinline",[returnObj,element,pElement]);
				if(returnObj.returnValue){return 0;}
			}
			return this.itemHeight;
		}
		return (itemHeight=element.LineHeight)?itemHeight:this.itemHeight;
	},onElementChange: function(element,changedFields)
	{
		var gantt=this.gantt;
		if(gantt.inline)
		{
			if(!_OBS_13(changedFields,_OBS_1[139]) && !_OBS_13(changedFields,_OBS_1[140])){return;}
			var startElement=null;
			
			
			if(gantt.getElementDrawObj(element)._height!=gantt.getElementHeight(element))
			{
				startElement=element;
			}
			else
			{
				var nextElement=element.getNextSibling();
				if(nextElement && gantt.getElementDrawObj(nextElement)._height!=gantt.getElementHeight(nextElement))
				{
					startElement=nextElement;
				}
			}
			for(var t=startElement;t;t=t.getNextSibling())
			{
				if(gantt.getElementDrawObj(t)._height==gantt.getElementHeight(t)){break;}
				_OBS_35(gantt,_OBS_1[54],[t,gantt.getElementHeight(t),gantt.getElementDrawObj(t)._height]);
			}
		}
		if(_OBS_13(changedFields,_OBS_1[126]))
		{
			_OBS_35(gantt,_OBS_1[54],[element,element.LineHeight,gantt.getElementDrawObj(element)._height]);
		}
	},remove: function()
	{
		var gantt=this.gantt;
		delete gantt.getElementHeight;
		delete gantt.removeElementDrawObj;
		delete gantt.getElementDrawObj;
		delete this.gantt
	}});
	 function SFGanttViewItemsControl(elementType)
	{
		this.elementType=elementType;
	}
	SFGanttViewItemsControl.prototype=new SFGanttControl()
	_OBS_4(SFGanttViewItemsControl.prototype,{initialize: function(gantt)
	{
		if(!gantt.getLayout || !gantt.getLayout(_OBS_1[47])){return false;}
		_OBS_4(this,{gantt:gantt,heightSpan:[0,0],viewElements:[]});
		gantt.getViewTop=_OBS_22(this,this.getViewTop);
		gantt.getViewElements=_OBS_22(this,this.getViewElements);
		gantt.getViewIndex=_OBS_22(this,this.getViewIndex);
		gantt.getElementViewTop=_OBS_22(this,this.getElementViewTop);
		gantt.setScrollTop=_OBS_22(this,this.setScrollTop);
		gantt.scrollToElement=_OBS_22(this,this.scrollToElement);
		this.listeners=[
			_OBS_28(gantt,_OBS_1[94],this,this.onGanttInit),
			_OBS_28(gantt,_OBS_1[91],this,this.onScroll),
			_OBS_28(gantt,_OBS_1[92],this,this.showViewElements),
			_OBS_28(gantt,_OBS_1[54],this,this.onHeightChange)
		];
		return true;
	},onGanttInit: function()
	{
		var gantt=this.gantt,data=gantt.getData(),el=this.elementType.toLowerCase();
		this.listeners=this.listeners.concat([
			_OBS_28(data,_OBS_1[143]+el+_OBS_1[144],this,this.onElementMove),
			_OBS_28(data,_OBS_1[143]+el+"add",this,this.onElementAdd),
			_OBS_28(data,_OBS_1[143]+el+_OBS_1[148],this,this.onElementDelete),
			_OBS_28(data,_OBS_1[143]+el+_OBS_1[141],this,this.onElementChange)
		]);
		this.setScrollTop(gantt.scrollTop?gantt.scrollTop:0);
	},setScrollTop: function(top)
	{
		this.onScroll(top);
		var gantt=this.gantt,scrollDiv=gantt.getLayout(_OBS_1[47]);
		if(gantt.forPrint)
		{
			scrollDiv.firstChild.style.position=_OBS_1[178];
			scrollDiv.firstChild.style.top=-top+"px";
		}
		else
		{
			scrollDiv.scrollTop=top;
		}
	},getScrollTop: function()
	{
		return this.scrollTop?this.scrollTop:0;
	},onScroll: function(scrollTop)
	{
		if(scrollTop)
		{
			this.scrollTop=scrollTop;
		}
		else
		{
			var gantt=this.gantt,scrollDiv=gantt.getLayout(_OBS_1[47]);
			this.scrollTop=gantt.forPrint?(-parseInt(scrollDiv.firstChild.style.top)):scrollDiv.scrollTop;
		}
		this.showViewElements();
	},inViewElement: function(element,index)
	{
		var gantt=this.gantt;
		if(index<0)
		{
			this.viewElements.push(element);
			index=this.viewElements.length-1;
		}
		else
		{
			this.viewElements.splice(index,0,element);
		}
		if(this.viewElements[1] && index==0)
		{
			this.heightSpan[0]-=gantt.getElementHeight(element);
		}
		else
		{
			this.heightSpan[1]+=gantt.getElementHeight(element);
		}
		_OBS_35(gantt,_OBS_1[86],[this.heightSpan]);
		
		
		_OBS_35(gantt,this.elementType.toLowerCase()+"inview",[element,index]);
	},outViewElement: function(index)
	{
		if(index<0){index=this.viewElements.length-1}
		if(index<0){return;}
		var element=this.viewElements.splice(index,1)[0],gantt=this.gantt,drawObj=gantt.getElementDrawObj(element);
		if(index==0 && this.viewElements.length>0 && !element.isHidden())
		{
			this.heightSpan[0]+=drawObj.height;
		}
		else
		{
			this.heightSpan[1]-=drawObj.height;
		}
		_OBS_35(gantt,_OBS_1[86],[this.heightSpan]);
		
		
		_OBS_35(gantt,this.elementType.toLowerCase()+"outview",[element,index]);
	},getViewIndex: function(element)
	{
		for(var i=this.viewElements.length-1;i>=0;i--)
		{
			if(element==this.viewElements[i]){return i;}
		}
		return -1;
	},getViewTop: function()
	{
		return this.heightSpan[0];
	},resetHeightSpan: function()
	{
		var firstView=this.viewElements[0],height=0,found=false,gantt=this.gantt;
		if(firstView)
		{
			for(var t=gantt.getData().getRootElement(this.elementType).getFirstChild();t;t=t.getNextView())
			{
				if(t==firstView)
				{
					found=true;
					break;
				}
				height+=gantt.getElementDrawObj(t).height;
			}
			if(found)
			{
				var span=this.heightSpan[0]-height;
				this.heightSpan[0]=height;
				this.heightSpan[1]-=span;
			}
		}
		else
		{
			this.heightSpan=[0,0];
		}
		_OBS_35(gantt,_OBS_1[86],[this.heightSpan]);
	},delayShowViewElements: function()
	{
		if(!this.dst){this.dst=window.setInterval(_OBS_22(this,this.onShowTime),32);}
		this.showChanged=true;
		this.showIdleTimes=0;
	},onShowTime: function()
	{
		if(!this.showChanged)
		{
			this.showIdleTimes++;
			if(this.showIdleTimes>4)
			{
				window.clearInterval(this.dst);
				delete this.dst
				this.showViewElements(true);
			}
			return;
		}
		this.showChanged=false;
	},showViewElements: function(check)
	{
		var gantt=this.gantt,scrollDiv=gantt.getLayout(_OBS_1[47]),enlargeHeight=gantt.viewEnlargeHeight,bufferHeight=gantt.viewBufferHeight+enlargeHeight;
		var startHeight=this.getScrollTop()-enlargeHeight;
		var endHeight=startHeight+scrollDiv.clientHeight+enlargeHeight*2;
		
		if(check && this.viewElements.length>1)
		{
			var height=this.heightSpan[0];
			var j=0;
			for(var i=0;i<this.viewElements.length-1;i++)
			{
				var startElement=this.viewElements[i],endElement=this.viewElements[i+1];
				if(startElement.getNextView()!=endElement)
				{
					for(var element=startElement.getNextView();element && element!=endElement;element=element.getNextView())
					{
						height+=gantt.getElementHeight(element);
						this.inViewElement(element,i+(++j),true);
						if(height>endHeight)
						{
							break;
						}
					}
					i+=j;
					j=0;
				}
				else
				{
					height+=gantt.getElementHeight(startElement);
				}
				if(height>endHeight)
				{
					this.removeViewElements(i+j);
					this.heightSpan[1]=height;
					_OBS_35(gantt,_OBS_1[86],[this.heightSpan]);
					break;
				}
			}
		}
		while(this.viewElements[0] && this.heightSpan[0]+gantt.getElementHeight(this.viewElements[0])<startHeight-bufferHeight)
		{
			this.outViewElement(0);
		}
		while(this.viewElements[0]&& this.heightSpan[1]-gantt.getElementHeight(this.viewElements[this.viewElements.length-1])>endHeight+bufferHeight)
		{
			this.outViewElement(-1);
		}
		if(!this.viewElements[0])
		{
			var height=0,element=gantt.data.getRootElement(this.elementType).getNext();
			while(height<startHeight && element)
			{
				if(height+gantt.getElementHeight(element)>=startHeight){break;}
				height+=gantt.getElementHeight(element);
				element=element.getNextView();
			}
			if(!element)
			{
				if(height>0)
				{
					this.setScrollTop(height);
				}
				return;
			}
			this.heightSpan=[height,height];
			_OBS_35(gantt,_OBS_1[86],[this.heightSpan]);
			this.inViewElement(element,-1);
		}
		while(this.heightSpan[1]<endHeight)
		{
			var element=this.viewElements[this.viewElements.length-1].getNextView();
			if(!element){break}
			this.inViewElement(element,-1);
		}
		while(this.heightSpan[0]>startHeight)
		{
			var element=this.viewElements[0].getPreviousView();
			if(!element){break}
			this.inViewElement(element,0);
		}
	},getElementViewTop: function(element)
	{
		var firstElement=this.viewElements[0];
		var gantt=this.gantt,dir=gantt.data.compareElement(firstElement,element)>0,height=0;
		for(var t=element;t;t=dir?t.getPreviousView():t.getNextView())
		{
			if(t==element && dir){continue;}
			if(t==firstElement && !dir){break;}
			height+=gantt.getElementHeight(t)*(dir?1:-1);
			if(t==firstElement){break;}
		}
		return this.getViewTop()+height;
	},removeViewElements: function(index)
	{
		for(var i=this.viewElements.length-1;i>index;i--)
		{
			this.outViewElement(-1,true);
		}
	},getViewElements: function()
	{
		return this.viewElements;
	},onElementChange: function(element,name,value,bValue)
	{
		switch(name)
		{
			case _OBS_1[127]:
				
				if(element.isHidden()){return;}
				
				var needRefresh=this.viewElements[0] && this.gantt.data.compareElement(element,this.viewElements[0])>0;
				var collapse=element.Collapse;
				if(collapse)
				{
					for(var i=0;i<this.viewElements.length;i++)
					{
						if(element!=this.viewElements[i] && element.contains(this.viewElements[i]))
						{
							this.outViewElement(i,true);
							i--;
						}
					}
				}
				if(needRefresh){this.resetHeightSpan();}
				this.showViewElements(!collapse);
				break;
			case _OBS_1[126]:

		}
	},onHeightChange: function(element,value,bValue)
	{
		
		if(element.isHidden()){return;}
		
		if(this.viewElements[0] && this.gantt.data.compareElement(element,this.viewElements[0])>=0)
		{
			var span=value-(bValue?bValue:this.gantt.itemHeight);
			this.heightSpan[0]+=span;
			this.heightSpan[1]+=span;
			_OBS_35(this.gantt,_OBS_1[86],[this.heightSpan]);
		}
		var index=this.getViewIndex(element);
		this.outViewElement(index,true);
		this.gantt.removeElementDrawObj(element);
		this.delayShowViewElements();
	},onElementAdd: function(element)
	{
		
		if(element.isHidden()){return;}
		var flag=false;
		
		if(this.viewElements[0] && this.gantt.data.compareElement(element,this.viewElements[0])>0)
		{
			var height=this.gantt.getElementHeight(element);
			this.heightSpan[0]+=height;
			this.heightSpan[1]+=height;
			
			_OBS_35(this.gantt,_OBS_1[86],[this.heightSpan]);
			flag=true;
		}
		if(flag || this.viewElements.length==0 || _OBS_13(this.viewElements,element.getNextView()) || _OBS_13(this.viewElements,element.getPreviousView()))
		{
			this.delayShowViewElements();
		}
	},onElementMove: function(element,pElement,preElement)
	{
		
		
		
		
		var data=this.gantt.data;
		var oIsUS=(!pElement.Collapse && !pElement.isHidden())&& this.viewElements[0] && data.compareElement((preElement?preElement.getLastDescendant(true):pElement),this.viewElements[0])>0;
		var nIsUS=(!element.isHidden())&& this.viewElements[0] && data.compareElement(element,this.viewElements[0])>0;
		
		for(var i=0;i<=this.viewElements.length;i++)
		{
			if(element.contains(this.viewElements[i]))
			{
				var t=this.viewElements[i];
				this.outViewElement(i,true);
				this.gantt.removeElementDrawObj(t);
				i--;
			}
		}
		if(oIsUS!=nIsUS){this.resetHeightSpan();}
		this.delayShowViewElements();
	},onElementDelete: function(element,pElement,preElement)
	{
		
		if(pElement.Collapse || pElement.isHidden()){return;}
		
		var lastView=preElement?preElement.getLastDescendant(true):pElement,viewElements=this.viewElements;
		var needRefresh=viewElements[0] && this.gantt.data.compareElement(lastView,viewElements[0])>0;
		
		for(var i=viewElements.length-1;i>=0;i--)
		{
			if(viewElements[i].isHidden())
			{
				var t=viewElements[i];
				this.outViewElement(i,true);
			}
		}
		if(needRefresh){this.resetHeightSpan();}
		if(this.Selected){this.removeSelectedElement(element);}
		this.delayShowViewElements();
	},scrollToElement: function(element,offset)
	{
		offset=offset?offset:0;
		this.gantt.setScrollTop(Math.max(0,this.gantt.getElementViewTop(element)-offset));
	},remove: function()
	{
		var gantt=this.gantt;
		delete gantt.getViewTop;
		delete gantt.getViewElements;
		delete gantt.getViewIndex;
		delete gantt.getElementViewTop;
		delete gantt.setScrollTop;
		delete gantt.scrollToElement;
		delete this.viewElements;
		SFGanttControl.prototype.remove.apply(this,arguments);
	}});
	 function SFGanttWorkingMask()
	{
	}
	SFGanttWorkingMask.prototype=new SFGanttControl()
	_OBS_4(SFGanttWorkingMask.prototype,{initialize: function(gantt)
	{
		if(gantt.disableWorkingMask || !gantt.getTimePanel || !gantt.getCalList){return false;}
		var container=gantt.getTimePanel();
		if(!container){return false;}
		_OBS_53(this,gantt.config.getConfigObj("SFGanttWorkingMask"));
		this.gantt=gantt;
		
		var div=this.div=gantt.container.ownerDocument.createElement("div");
		_OBS_4(div.style,{position:_OBS_1[179],fontSize:'0px',left:'0px',top:'0px',height:_OBS_1[177],width:_OBS_1[177],zIndex:10});
		container.appendChild(div);
		this.listeners=[
			_OBS_28(gantt,_OBS_1[94],this,this.onGanttInit),
			_OBS_28(gantt,_OBS_1[84],this,this.reDraw),
			_OBS_28(gantt,_OBS_1[83],this,this.reDraw),
			_OBS_28(gantt,_OBS_1[144],this,this.reDraw)
		];
		this.reDraw();
		return true;
	},onGanttInit: function()
	{
		this.calendar=this.gantt.getData().getCalendar();
		this.reDraw();
	},reDraw: function()
	{
		var gantt=this.gantt,scale=gantt.getScale(),startTime=gantt.getStartTime(),cals=gantt.getCalList();
		if(!cals || !cals[0] || !this.calendar){return;}
		this.showSegmentations(gantt.getScale(),gantt.getStartTime().valueOf(),cals[0]);
	},showSegmentations: function(scale,startTime,cal)
	{
		if(this.cal!=cal || !this.drawStart || Math.abs(startTime-this.drawStart)/scale>10000)
		{
			this.clear();
			_OBS_4(this,{scale:scale,drawStart:startTime,cal:cal});
			this.div.style.left=this.gantt.getTimePanelPosition(startTime)+"px";
		}
		if(this.scale!=scale)
		{
			for(var child=this.div.firstChild;child;child=child.nextSibling)
			{
				_OBS_4(child.style,{
					left:(child.sTime-this.drawStart)/scale+1+"px",
					width:(child.eTime-child.sTime)/scale+"px"
				})
			}
			this.div.style.left=this.gantt.getTimePanelPosition(this.drawStart)+"px";
			this.scale=scale;
		}
		var endTime=startTime+this.div.offsetWidth*scale;
		var calDiv=this.div;

		var osTime=calDiv.firstChild?calDiv.firstChild.sTime:Number.MAX_VALUE;
		var oeTime=calDiv.lastChild?calDiv.lastChild.eTime:Number.MIN_VALUE;
		if(startTime>(calDiv.firstChild?calDiv.firstChild.eTime:Number.MAX_VALUE))
		{
			while(calDiv.firstChild && calDiv.firstChild.eTime<startTime)
			{
				_OBS_29(calDiv.firstChild);
			}
			osTime=calDiv.firstChild?calDiv.firstChild.sTime:Number.MAX_VALUE
		}
		if((calDiv.lastChild?calDiv.lastChild.sTime:Number.MIN_VALUE)>endTime)
		{
			while(calDiv.lastChild && calDiv.lastChild.sTime>endTime)
			{
				_OBS_29(calDiv.lastChild);
			}
			oeTime=calDiv.lastChild?calDiv.lastChild.eTime:Number.MIN_VALUE
		}
		if(startTime<osTime)
		{
			this.addMask(startTime,Math.min(osTime,endTime),cal,calDiv,scale,-1);
			osTime=calDiv.firstChild?calDiv.firstChild.sTime:Number.MAX_VALUE;
			oeTime=calDiv.lastChild?calDiv.lastChild.eTime:Number.MIN_VALUE;
		}
		if(oeTime<endTime)
		{
			this.addMask(Math.max(oeTime,startTime),endTime,cal,calDiv,scale,1);
		}
	},addMask: function(startTime,endTime,cal,calDiv,scale,position)
	{
		var sTime=parseInt(cal.getFloorTime(new Date(startTime)).valueOf()),doc=this.div.ownerDocument;
		var lastAdd=null;
		while(sTime<endTime)
		{
			var eTime=parseInt(cal.getNextTime(new Date(sTime)).valueOf());
			var workTime=this.calendar.getWorkTime(new Date(sTime));
			if(workTime[0]>=eTime.valueOf())
			{
				var div=doc.createElement("div");
				_OBS_4(div,{sTime:sTime,eTime:eTime});
				_OBS_4(div.style,{position:_OBS_1[179],left:(sTime-this.drawStart)/scale+1+"px",top:'0px',width:(eTime-sTime)/scale+"px",height:_OBS_1[177]});
				this.gantt.setBackgroundImage(div,"map_mask");
				if(position==-1)
				{
					if(lastAdd==null)
					{
						calDiv.insertBefore(div,calDiv.firstChild);
					}
					else if(lastAdd.nextSibling==null)
					{
						calDiv.appendChild(div);
					}
					else
					{
						calDiv.insertBefore(div,lastAdd.nextSibling);
					}
				}
				else
				{
					calDiv.appendChild(div);
				}
				lastAdd=div;
			}
			sTime=eTime;
		}
	},clear: function()
	{
		_OBS_29(this.div,true);
	}});
	 function SFGanttZoomControl()
	{
	}
	SFGanttZoomControl.prototype=new SFGanttControl()
	_OBS_4(SFGanttZoomControl.prototype,{initialize: function(gantt,container)
	{
		this.gantt=gantt;
		this.levels=[
			3*60000/6,	
			30*60000/6,	
			3600000/6,	
			4*3600000/6,	
			12*3600000/6,
			24*3600000/6,	
			96*3600000/6,	
			192*3600000/6,	
			576*3600000/6,	
			1728*3600000/6	
		];
		_OBS_4(gantt,{
			getZoomScale:_OBS_22(this,this.getZoomScale),
			zoomIn:_OBS_22(this,this.zoomIn),
			zoomOut:_OBS_22(this,this.zoomOut),
			zoomTo:_OBS_22(this,this.zoomTo),
			getZoom:_OBS_22(this,this.getZoom),
			show:_OBS_22(this,this.show)
		});
		gantt.showMap=gantt.show;
		this.listeners=[
			_OBS_28(gantt,_OBS_1[94],this,this.onScaleChange),
			_OBS_28(gantt,_OBS_1[83],this,this.onScaleChange)
		];
		return true;
	},getZoomScale: function(scale,dir)
	{
		return this.levels[this.getZoomIndex(scale,dir)];
	},getZoomIndex: function(scale,dir)
	{
		dir=dir?dir:0;
		var levels=this.levels,len=levels.length;
		for(var i=0;i<len;i++)
		{
			var level=levels[i];
			if(scale<=level)
			{
				if(i>0 && ((dir==1) || (dir==0 && scale/(levels[i-1])<level/scale)))
				{
					return i-1;
				}
				return i;
			}
		}
		return len-1;
	},onScaleChange: function()
	{
		this.zoomIndex=this.getZoomIndex(this.gantt.getScale());
	},zoomIn: function(){this.zoomTo(this.zoomIndex-1);},zoomOut: function(){this.zoomTo(this.zoomIndex+1);},zoomTo: function(zoomIndex)
	{
		if(!this.levels[zoomIndex]){return;}
		var oZoom=this.zoomIndex;
		this.zoomIndex=zoomIndex;
		this.gantt.setScale(this.levels[zoomIndex]);
		
		_OBS_35(this,"zoom",[zoomIndex,oZoom]);
	},getZoom: function()
	{
		return this.zoomIndex;
	},show: function(startTime,zoomIndex)
	{
		var scale=this.levels[zoomIndex];
		scale=scale?scale:zoomIndex;
		var gantt=this.gantt;
		if(startTime){gantt.setStartTime(startTime);}
		if(scale){gantt.setScale(scale);}
	},remove: function()
	{
		var gantt=this.gantt;
		delete gantt.getZoomScale;
		delete gantt.showMap
		delete gantt.zoomIn
		delete gantt.zoomOut
		delete gantt.zoomTo
		delete gantt.getZoom;
		delete gantt.show
		SFGanttControl.prototype.remove.apply(this,arguments);
	}});
	 function SFGanttField()
	{
		if(arguments.length<=0){return;}
		_OBS_4(this,{width:100,headText:"",headStyle:{textAlign:_OBS_1[81]},bodyStyle:{textAlign:_OBS_1[95]},inputStyle:{}});
		var obj=arguments[0];
		if(typeof(obj)!=_OBS_1[184])
		{
			var argu=arguments;
			obj={};
			if(argu[0]){obj.width=argu[0];}
			if(argu[1]){obj.headText=argu[1];}
			if(argu[2]){obj.headFunc=argu[2];}
			if(argu[3]){obj.bodyFunc=argu[3];}
			if(argu[4]){obj.inputFunc=argu[4];}
			if(argu[5]){obj.inputData=argu[5];}
			if(argu[6]){obj.bodyData=argu[6];}
		}
		_OBS_4(this,obj);
	}
	_OBS_4(SFGanttField.prototype,{setWidth: function(width){this.width=parseInt(width);},setHeadText: function(text){this.headText=text;},setHeadAlign: function(align){this.setHeadStyle({textAlign:align});},setHeadColor: function(color){this.setHeadStyle({color:color})},setHeadBgColor: function(color){this.setHeadStyle({backgroundColor:color});},setHeadStyle: function(obj){_OBS_4(this.headStyle,obj);},setBodyAlign: function(align){this.setBodyStyle({textAlign:align});},setBodyColor: function(color){this.setBodyStyle({color:color});},setBodyBgColor: function(color){this.setBodyStyle({backgroundColor:color});},setBodyStyle: function(obj){_OBS_4(this.bodyStyle,obj);},setInputHandle: function(handle){this.inputFunc=handle;},setInputStyle: function(obj){_OBS_4(this.inputStyle,obj);},setReadOnly: function(ReadOnly){this.ReadOnly=ReadOnly;},showHead: function(cell,list)
	{
		_OBS_29(cell,true);
		_OBS_4(cell.style,this.headStyle);
		return this.headFunc(cell,list);
	},showBody: function(cell,element,list)
	{
		_OBS_29(cell,true);
		_OBS_4(cell.style,this.bodyStyle);
		return this.bodyFunc(cell,element,list);
	},showInput: function(cell,element,list)
	{
		_OBS_29(cell,true);
		_OBS_4(cell.style,this.bodyStyle);
		_OBS_4(cell.style,this.inputStyle);
		return this.inputFunc(cell,element,list);
	},checkUpdate: function(changedFields)
	{
		if(!this.bodyData){return false;}
		var datas=this.bodyData.split(",");
		for(var j=0;j<datas.length;j++)
		{
			for(var k=0;k<changedFields.length;k++)
			{
				if(changedFields[k]==datas[j])
				{
					return true;
				}
			}
		}
		return false;
	},headFunc: function(cell)
	{
		cell.innerHTML=this.headText;
	},bodyFunc: function(cell,element,list)
	{
		var value=element[this.bodyData];
		value=(typeof(value)!=_OBS_1[174])?value:"";
		cell.appendChild(cell.ownerDocument.createTextNode(value));
	},createInput: function(div)
	{
		var input=div.ownerDocument.createElement(_OBS_1[49]);
		_OBS_4(input.style,{width:_OBS_1[177],height:_OBS_1[177],border:'solid 2px #000000',overflow:_OBS_1[180]});
		_OBS_31(input,_OBS_1[73],_OBS_27);
		_OBS_31(input,_OBS_1[170],_OBS_27);
		_OBS_31(input,_OBS_1[172],function(e)
		{
			_OBS_32(input.cml);
			input.cml=_OBS_31(input,_OBS_1[52],_OBS_27);
			_OBS_27(e);
		});
		_OBS_31(input,_OBS_1[24],_OBS_27);
		input.cml=_OBS_31(input,_OBS_1[52],_OBS_26);
		return input;
	},inputFunc: function(cell,element,list)
	{
		var inputData=this.inputData,field=this;
		var value=element[this.inputData];
		var input=this.createInput(cell,field,list);
		input.value=(typeof(value)!=_OBS_1[174])?value:"";
		_OBS_31(input,_OBS_1[23],function(e)
		{
			if(e.keyCode==27)
			{
				var value=element[inputData];
				input.value=(typeof(value)!=_OBS_1[174])?value:"";
			}
			if(e.keyCode==13)
			{
				element.setProperty(inputData,input.value);
				_OBS_29(cell,true);
				field.showBody(cell,element,list);
			}
		});
		_OBS_31(input,_OBS_1[141],function()
		{
			element.setProperty(inputData,input.value);
		});
		cell.appendChild(input);
		input.focus();
	}});
	 function _OBS_83(type,name)
	{
		var fields=SFGanttField[_OBS_1[30]+type];
		if(!fields || !fields[name]){_OBS_85(type,name,new SFGanttField(100,name));}
		if(!fields){fields=SFGanttField[_OBS_1[30]+type];}
		return fields[name];
	}
	 function _OBS_84(type,names)
	{
		var fields=[];
		for(var i=0;i<names.length;i++)
		{
			if(!names[i]){continue;}
			fields.push(_OBS_83(type,names[i]));
		}
		return fields;
	}
	 function _OBS_85(type,name,field)
	{
		var fields=SFGanttField[_OBS_1[30]+type];
		if(!fields){fields=SFGanttField[_OBS_1[30]+type]={};}
		fields[name]=field;
		field.Name=name;
	}
	 function _OBS_86(names){return _OBS_84(_OBS_1[160],names);}
	 function _OBS_87(names){return _OBS_84(_OBS_1[159],names);}
	 function _OBS_88(names){return _OBS_84(_OBS_1[158],names);}
	 function _OBS_89(name){return _OBS_83(_OBS_1[160],name);}
	 function _OBS_90(name){return _OBS_83(_OBS_1[159],name);}
	 function _OBS_91(name){return _OBS_83(_OBS_1[158],name);}
	 function _OBS_92(name,field){return _OBS_85(_OBS_1[160],name,field);}
	 function _OBS_93(name,field){return _OBS_85(_OBS_1[159],name,field);}
	 function _OBS_94(name,field){return _OBS_85(_OBS_1[158],name,field);}
	 function _OBS_95(name,width,headText,headFunc,bodyFunc,inputFunc,inputData,bodyData){_OBS_85(_OBS_1[160],name,new SFGanttField({width:width,headText:headText,headFunc:headFunc,bodyFunc:bodyFunc,inputFunc:inputFunc,inputData:inputData,bodyData:bodyData}));}
	 function _OBS_96()
	{
		if(SFGanttField.inited){return;}
		SFGanttField.inited=true;
		
		SFGanttField.NormalHead=SFGanttField.prototype.headFunc;
		SFGanttField.NormalBody=SFGanttField.prototype.bodyFunc;
		SFGanttField.BoolBody=SFGanttFieldBool.prototype.bodyFunc
		SFGanttField.BoolInput=SFGanttFieldBool.prototype.inputFunc
		SFGanttField.BoolCheckbox=SFGanttFieldBool.prototype.inputFunc
		SFGanttField.createInput=SFGanttField.prototype.createInput
		SFGanttField.NormalInput=SFGanttField.prototype.inputFunc;
		SFGanttField.DateBody=SFGanttFieldDateTime.prototype.bodyFunc;
		SFGanttField.DateInput=SFGanttFieldDateTime.prototype.inputFunc;
		var config=window._SFGantt_config.SFGanttField;
		var names=config.fieldTexts;
		
		
		_OBS_92("Empty",		new SFGanttField({width:36,ReadOnly:true}));
		
		_OBS_92("UID",		new SFGanttField({width:36,bodyData:"UID",headText:names.UID,ReadOnly:true,bodyStyle:{textAlign:_OBS_1[81]}}));
		
		_OBS_92('ID',			new SFGanttField({width:36,bodyData:'ID',headText:names.ID,ReadOnly:true,bodyStyle:{textAlign:_OBS_1[81]}}))
		
		_OBS_92("name",		new SFGanttField({width:120,bodyData:'Name',headText:names.TaskName}));
		
		_OBS_92('Name',		new SFGanttFieldTreeName({width:120,headText:names.TaskName}));
		
		_OBS_92(_OBS_1[137],	new SFGanttField({width:100,bodyData:_OBS_1[137],headText:names.OutlineNumber,ReadOnly:true}));
		
		var field=new SFGanttFieldIcon({width:32,headText:names.StatusIcon});
		field.addIcon(function(element,gantt)
			{
				if(element.PercentComplete<100){return;}
				var img=this.createImage(gantt,"icon_finished");
				if(gantt.setTooltip)
				{
					gantt.setTooltip(img,function(tooltip)
					{
						if(tooltip.bindObject==img){return false;}
						tooltip.bindObject=img;
						tooltip.setContent(gantt.container.ownerDocument.createTextNode(_OBS_10(config.tooltipPercentComplete,_OBS_9(element.Finish,config.dateShowFormat))))
						return true;
					});
				}
				return img;
			},_OBS_1[134]);
		field.addIcon(function(element,gantt)
			{
				if(!element.ConstraintType || element.ConstraintType<=1){return;}
				var img=this.createImage(gantt,"icon_constraint"+element.ConstraintType);
				if(gantt.setTooltip)
				{
					gantt.setTooltip(img,function(tooltip)
					{
						if(tooltip.bindObject==img){return false;}
						tooltip.bindObject=img;
						var str=_OBS_10(config.tooltipConstraint,[config.constraintTypes[element.ConstraintType],_OBS_9(element.ConstraintDate,config.dateShowFormat)])
						tooltip.setContent(gantt.container.ownerDocument.createTextNode(str));
						return true
					});
				}
				return img;
			},"ConstraintType,ConstraintDate");
		field.addIcon(function(element,gantt)
			{
				if(!element.Notes){return;}
				var img=this.createImage(gantt,_OBS_1[29])
				if(gantt.setTooltip)
				{
					gantt.setTooltip(img,function(tooltip)
					{
						if(tooltip.bindObject==img){return false;}
						tooltip.bindObject=img;
						var str=_OBS_83(element.elementType,_OBS_1[133]).headText+": \""+element.Notes+"\"";
						tooltip.setContent(gantt.container.ownerDocument.createTextNode(str));
						return true;
					});
				}
				return img;
			},_OBS_1[133]);
		field.addIcon(function(element,gantt)
			{
				if(!element.HyperlinkAddress){return;}
				var link=gantt.container.ownerDocument.createElement("a");
				link.href=element.HyperlinkAddress;
				link.target='_blank';
				var img=this.createImage(gantt,_OBS_1[28]);
				link.appendChild(img)
				if(gantt.setTooltip)
				{
					gantt.setTooltip(img,function(tooltip)
					{
						if(tooltip.bindObject==img){return false;}
						tooltip.bindObject=img;
						var str=element.Hyperlink?element.Hyperlink:element.HyperlinkAddress;
						tooltip.setContent(gantt.container.ownerDocument.createTextNode(str));
						return true;
					});
				}
				return link;
			},_OBS_1[27]);
		_OBS_92(_OBS_1[26],		field);
		
		_OBS_92("Duration",		new SFGanttFieldDuration({width:60,bodyData:'Start,Finish',headText:names.Duration}));
		
		_OBS_92(_OBS_1[139],		new SFGanttFieldDateTime({width:100,bodyData:_OBS_1[139],headText:names.Start,disableSummaryEdit:true}));
		
		_OBS_92(_OBS_1[140],		new SFGanttFieldDateTime({width:100,bodyData:_OBS_1[140],headText:names.Finish,disableSummaryEdit:true}));
		
		_OBS_92(_OBS_1[133],		new SFGanttFieldLongText({width:100,bodyData:_OBS_1[133],headText:names.Notes}));
		
		_OBS_92(_OBS_1[128],		new SFGanttFieldSelecter({width:120,bodyData:_OBS_1[128],headText:names.ClassName}));
		_OBS_89(_OBS_1[128]).getOptions=true?(function(element,list)
		{
			return {TaskNormal:_OBS_1[25],TaskImportant:'TaskImportant'};
		}):null;
		
		_OBS_92(_OBS_1[132],	new SFGanttFieldSelecter({width:120,bodyData:_OBS_1[132],headText:names.ConstraintType,options:window._SFGantt_config.SFGanttField.constraintTypes}));
		
		_OBS_92(_OBS_1[131],	new SFGanttFieldDateTime({width:100,bodyData:_OBS_1[131],headText:names.ConstraintDate}));
		
		_OBS_92(_OBS_1[125],		new SFGanttFieldBool({width:30,bodyData:_OBS_1[125],headText:names.Critical}));
		
		_OBS_92(_OBS_1[65],		new SFGanttFieldSelected({width:30,headText:names.Selected}));
		
		_OBS_92(_OBS_1[159],		new SFGanttField({width:100,bodyData:_OBS_1[159],headText:names.Resource,bodyFunc:function(cell,task,list)
			{
				var ans=[],assignments=task.getAssignments();
				for(var i=0;i<assignments.length;i++)
				{
					var resource=assignments[i].getResource();
					if(resource)
					{
						var name=resource.Name;
						if(assignments[i].Units!=1)
						{
							name+='['+(assignments[i].Units*100)+'%]';
						}
						ans.push(name);
					}
				}
				cell.appendChild(cell.ownerDocument.createTextNode(ans.join(",")));
			},ReadOnly:true}));
		
		_OBS_92(_OBS_1[134],	new SFGanttFieldPercent({width:100,bodyData:_OBS_1[134],headText:names.PercentComplete}));
		
		_OBS_92(_OBS_1[130],	new SFGanttFieldDateTime({width:100,bodyData:_OBS_1[130],headText:names.ActualStart,disableSummaryEdit:true}));
		
		_OBS_92(_OBS_1[129],	new SFGanttFieldDateTime({width:100,bodyData:_OBS_1[129],headText:names.ActualFinish,disableSummaryEdit:true}));
		
		_OBS_92("ActualDuration",	new SFGanttFieldDuration({width:60,bodyData:'ActualStart,ActualFinish',headText:names.ActualDuration}));
		
		_OBS_92(_OBS_1[124],	new SFGanttFieldDateTime({width:100,bodyData:_OBS_1[124],headText:names.BaselineStart,disableSummaryEdit:true}));
		
		_OBS_92(_OBS_1[123],	new SFGanttFieldDateTime({width:100,bodyData:_OBS_1[123],headText:names.BaselineFinish,disableSummaryEdit:true}));


		
		
		_OBS_93("Empty",		new SFGanttField({width:36,ReadOnly:true}));
		
		_OBS_93("UID",		new SFGanttField({width:36,bodyData:"UID",headText:names.UID,ReadOnly:true,bodyStyle:{textAlign:_OBS_1[81]}}));
		
		_OBS_93('ID',			new SFGanttField({width:36,bodyData:'ID',headText:names.ID,ReadOnly:true,bodyStyle:{textAlign:_OBS_1[81]}}))
		
		_OBS_93("name",		new SFGanttField({width:120,bodyData:'Name',headText:names.ResourceName}));
		
		_OBS_93('Name',		new SFGanttFieldTreeName({width:120,headText:names.ResourceName}));
		
		_OBS_93(_OBS_1[137],	new SFGanttField({width:100,bodyData:_OBS_1[137],headText:names.OutlineNumber,ReadOnly:true}));
		
		var field=new SFGanttFieldIcon({width:32,headText:names.StatusIcon});
		field.addIcon(function(element,gantt)
			{
				if(!element.Notes){return;}
				var img=this.createImage(gantt,_OBS_1[29]);
				if(gantt.setTooltip)
				{
					gantt.setTooltip(img,function(tooltip)
					{
						if(tooltip.bindObject==img){return false;}
						tooltip.bindObject=img;
						var str=_OBS_83(element.elementType,_OBS_1[133]).headText+": \""+element.Notes+"\"";
						tooltip.setContent(gantt.container.ownerDocument.createTextNode(str));
						return true;
					});
				}
				return img;
			},_OBS_1[133]);
		field.addIcon(function(element,gantt)
			{
				if(!element.HyperlinkAddress){return;}
				var link=gantt.container.ownerDocument.createElement("a");
				link.href=element.HyperlinkAddress;
				link.target='_blank';
				var img=this.createImage(gantt,_OBS_1[28]);
				link.appendChild(img)
				if(gantt.setTooltip)
				{
					gantt.setTooltip(img,function(tooltip)
					{
						if(tooltip.bindObject==img){return false;}
						tooltip.bindObject=img;
						var str=element.Hyperlink?element.Hyperlink:element.HyperlinkAddress;
						tooltip.setContent(gantt.container.ownerDocument.createTextNode(str));
						return true;
					});
				}
				return link;
			},_OBS_1[27]);
		_OBS_93(_OBS_1[26],		field);
		
		_OBS_93(_OBS_1[133],		new SFGanttFieldLongText({width:100,bodyData:_OBS_1[133],headText:names.Notes}));
		
		_OBS_93(_OBS_1[128],		new SFGanttFieldSelecter({width:120,bodyData:_OBS_1[128],headText:names.ClassName}));
		_OBS_90(_OBS_1[128]).getOptions=true?(function(element,list)
		{
			return {ResourceNormal:'ResourceNormal',ResourceImportant:'ResourceImportant'};
		}):null;
		
		_OBS_93(_OBS_1[125],		new SFGanttFieldBool({width:30,bodyData:_OBS_1[125],headText:names.Critical}));
		
		_OBS_93(_OBS_1[65],		new SFGanttFieldSelected({width:30,headText:names.Selected}));
		
		_OBS_93(_OBS_1[160],		new SFGanttField({width:100,bodyData:_OBS_1[159],headText:names.Resource,bodyFunc:function(cell,resource,list)
			{
				var ans=[],assignments=resource.getAssignments();
				for(var i=0;i<assignments.length;i++)
				{
					var resource=assignments[i].getResource();
					if(resource)
					{
						var name=resource.Name;
						if(assignments[i].Units!=1)
						{
							name+='['+(assignments[i].Units*100)+'%]';
						}
						ans.push(name);
					}
				}
				cell.appendChild(cell.ownerDocument.createTextNode(ans.join(",")));
			},ReadOnly:true}));



		
		SFGanttField.linkFields={};
		
		_OBS_94(_OBS_1[120],		new SFGanttFieldSelecter({width:100,bodyData:_OBS_1[120],headText:names.LinkType,options:window._SFGantt_config.SFGanttField.linkTypes}));
		
		_OBS_94("FromTask",		new SFGanttFieldElement({width:100,bodyData:_OBS_1[113],headText:names.FromTask}));
		
		_OBS_94("ToTask",		new SFGanttFieldElement({width:100,bodyData:_OBS_1[114],headText:names.ToTask}));
	}
	_OBS_4(SFGanttField,{getField:_OBS_83,getFields:_OBS_84,setField:_OBS_85,getTaskFields:_OBS_86,getResourceFields:_OBS_87,getLinkFields:_OBS_88,getTaskField:_OBS_89,getResourceField:_OBS_90,getLinkField:_OBS_91,setTaskField:_OBS_92,setResourceField:_OBS_93,setLinkField:_OBS_94,addTaskField:_OBS_95,init:_OBS_96});
	 function SFGanttFieldBool()
	{
		if(arguments.length<=0){return}
		SFGanttField.apply(this,arguments);
		this.inputData=this.bodyData;
	}
	SFGanttFieldBool.prototype=new SFGanttField()
	_OBS_4(SFGanttFieldBool.prototype,{bodyFunc: function(cell,element,list)
	{
		if(!this.ReadOnly)
		{
			this.inputFunc(cell,element,list);
			return;
		}
		var value=element[this.bodyData];
		var boolTypes=list.gantt.config.getConfig(_OBS_1[22])
		cell.appendChild(cell.ownerDocument.createTextNode(value?boolTypes[1]:boolTypes[0]));
	},inputFunc: function(cell,element,list)
	{
		var inputData=this.inputData,field=this;
		var value=element[this.bodyData];
		var input=cell.ownerDocument.createElement(_OBS_1[49]);
		input.type=_OBS_1[48];
		cell.appendChild(input);
		input.checked=!!value;
		_OBS_31(input,_OBS_1[73],function(e)
		{
			var btn=_OBS_39(e);
			if(btn && btn!=1){return;}
			element.setProperty(inputData,input.checked);
			_OBS_27(e);
		});
	}});
	 function SFGanttFieldPercent()
	{
		SFGanttField.apply(this,arguments);
		this.inputFunc=this.bodyFunc;
	}
	SFGanttFieldPercent.prototype=new SFGanttField()
	_OBS_4(SFGanttFieldPercent.prototype,{bodyFunc: function(cell,element,list)
	{
		var value=element[this.bodyData],doc=cell.ownerDocument;
		value=(typeof(value)!=_OBS_1[174])?value:0;
		value=Math.max(0,Math.min(value,100));
		var div=doc.createElement("div");
		_OBS_4(div.style,{position:_OBS_1[178],width:'90%',height:_OBS_1[177],backgroundColor:_OBS_1[74],border:_OBS_1[51],textAlign:_OBS_1[81]});
		cell.appendChild(div);
		var span=doc.createElement("div");
		_OBS_4(span.style,{position:_OBS_1[179],left:'0px',top:'0px',width:value+'%',height:_OBS_1[177],backgroundColor:_OBS_1[50],zIndex:2});
		div.appendChild(span);
		if(!this.ReadOnly)
		{
			var bar=doc.createElement("div");
			_OBS_4(bar.style,{position:_OBS_1[179],left:value+'%',top:'0px',width:'2px',height:_OBS_1[177],backgroundColor:'blue',zIndex:3});
			_OBS_11(bar,_OBS_1[79]);
			_OBS_59(bar,_OBS_22(this,this.onBarMove(element,div)),{container:div});
			div.appendChild(bar);
		}
		var text=doc.createElement("span");
		_OBS_4(text.style,{position:_OBS_1[178],zIndex:4});
		text.appendChild(doc.createTextNode(value+'%'));
		div.appendChild(text);
	},onBarMove: function(element,div)
	{
		return function(sp,lp,type)
		{
			var width=Math.min(Math.max(lp[0],0),div.offsetWidth-2);
			var percent=Math.round(100*width/(div.offsetWidth-2));
			if(type!="end")
			{
				div.firstChild.style.width=width+"px";
				div.firstChild.nextSibling.style.left=width+"px";
				div.lastChild.nodeValue=percent+'%';
			}
			else
			{
				element.setProperty(this.bodyData,percent);
			}
		}
	}});
	 function SFGanttFieldElement()
	{
		SFGanttField.apply(this,arguments);
		this.ReadOnly=true;
	}
	SFGanttFieldElement.prototype=new SFGanttField()
	_OBS_4(SFGanttFieldElement.prototype,{bodyFunc: function(cell,element,list)
	{
		var target=element[this.bodyData];
		if(!target){return;}
		var info="("+_OBS_83(target.elementType,"UID").headText+' '+target.UID+") "+target.Name;
		cell.appendChild(cell.ownerDocument.createTextNode(info));
	}});
	 function SFGanttFieldSelected()
	{
		SFGanttFieldBool.apply(this,arguments);
		this.bodyData=_OBS_1[65];
	}
	SFGanttFieldSelected.prototype=new SFGanttField()
	_OBS_4(SFGanttFieldSelected.prototype,{bodyFunc: function(cell,element,list)
	{
		if(!this.ReadOnly)
		{
			this.inputFunc(cell,element,list);
			return;
		}
		var value=element[this.bodyData];
		var boolTypes=list.gantt.config.getConfig(_OBS_1[22])
		cell.appendChild(cell.ownerDocument.createTextNode(value?boolTypes[1]:boolTypes[0]));
	},inputFunc: function(cell,element,list)
	{
		var inputData=this.inputData,field=this;
		var value=element[this.bodyData];
		var input=cell.ownerDocument.createElement(_OBS_1[49]);
		input.type=_OBS_1[48];
		cell.appendChild(input);
		input.checked=!!value;
		_OBS_31(input,_OBS_1[170],_OBS_27);
		_OBS_31(input,_OBS_1[172],_OBS_27);
		_OBS_31(input,_OBS_1[73],function(e)
		{
			_OBS_27(e);
			element.setProperty(_OBS_1[65],input.checked);
		});
	}});
	 function SFGanttFieldLongText()
	{
		SFGanttField.apply(this,arguments);
		this.inputData=this.bodyData;
	}
	SFGanttFieldLongText.prototype=new SFGanttField()
	_OBS_4(SFGanttFieldLongText.prototype,{inputFunc: function(cell,element,list)
	{
		var inputData=this.inputData,field=this;
		var value=element[inputData];
		var position=_OBS_36(cell,list.container);
		var input=cell.ownerDocument.createElement("textarea");
		_OBS_4(input.style,{position:_OBS_1[179],left:position[0]+"px",top:position[1]+"px",width:(this.width-2)+"px",height:"100px",border:_OBS_1[51],overflow:_OBS_1[180],zIndex:100});
		_OBS_31(input,_OBS_1[73],_OBS_27);
		_OBS_31(input,_OBS_1[170],_OBS_27);
		_OBS_31(input,_OBS_1[172],function(e)
		{
			_OBS_32(input.cml);
			input.cml=_OBS_31(input,_OBS_1[52],_OBS_27);
			_OBS_27(e);
		});
		_OBS_31(input,_OBS_1[24],_OBS_27);
		input.cml=_OBS_31(input,_OBS_1[52],_OBS_26);
		input.value=(typeof(value)!=_OBS_1[174])?value:"";
		if(!this.ReadOnly)
		{
			_OBS_31(input,_OBS_1[23],function(e)
			{
				if(e.keyCode==27)
				{
					var value=element[inputData];
					input.value=(typeof(value)!=_OBS_1[174])?value:"";
				}
			});
			_OBS_31(input,_OBS_1[141],function()
			{
				element.setProperty(inputData,input.value);
			});
		}
		else
		{
			input.disabled=true;
		}
		_OBS_31(input,"blur",function()
		{
			_OBS_29(input);
		});
		list.container.appendChild(input);
		input.focus();
	}});
	 function SFGanttFieldDateTime()
	{
		SFGanttField.apply(this,arguments);
		this.inputData=this.bodyData;
	}
	SFGanttFieldDateTime.prototype=new SFGanttField()
	_OBS_4(SFGanttFieldDateTime.prototype,{bodyFunc: function (cell,element,list)
	{
		var str=element[this.bodyData]?_OBS_9(element[this.bodyData],list.gantt.config.getConfig("SFGanttField/dateShowFormat")):"";
		cell.appendChild(cell.ownerDocument.createTextNode(str));
	},inputFunc: function(cell,element,list)
	{
		if(this.disableSummaryEdit && element.Summary)
		{
			_OBS_29(cell,true);
			this.showBody(cell,element,list);
			return;
		}
		var inputData=this.inputData,field=this;
		var value=element[field.inputData];
		value=(typeof(value)!=_OBS_1[174])?value:new Date();
		var input=SFGanttField.createInput(cell,field,list);
		var config=list.gantt.config.getConfig(_OBS_1[21]);
		input.value=_OBS_9(value,config.dateInputFormat);
		_OBS_31(input,_OBS_1[23],function(e)
		{
			if(e.keyCode==27)
			{
				var value=element[field.inputData];
				input.value=_OBS_9(value,config.dateInputFormat);
			}
			if(e.keyCode==13)
			{
				if(input.value)
				{
					var value=_OBS_7(input.value);
					if(value && !isNaN(value))
					{
						element.setProperty(inputData,value);
						_OBS_29(cell,true);
						field.showBody(cell,element,list);
					}
					else
					{
						if(config.noticeWrongFormat){alert(config.noticeWrongFormat);}
						input.focus();
					}
				}
				else
				{
					element.setProperty(inputData,null);
				}
			}
		});
		_OBS_31(input,_OBS_1[141],function()
		{
			if(input.value)
			{
				var value=_OBS_7(input.value);
				if(value && !isNaN(value))
				{
					element.setProperty(inputData,value);
				}
				else
				{
					if(config.noticeWrongFormat){alert(config.noticeWrongFormat);}
					input.focus();
				}
			}
			else
			{
				element.setProperty(inputData,null);
			}
		});
		
		cell.appendChild(input);
		input.focus();
	}});
	 function SFGanttFieldDuration()
	{
		SFGanttField.apply(this,arguments);
		this.ReadOnly=true;
	}
	SFGanttFieldDuration.prototype=new SFGanttField()
	_OBS_4(SFGanttFieldDuration.prototype,{bodyFunc: function (cell,element,list)
	{
		var data=this.bodyData.split(","),start=element[data[0]],finish=element[data[1]],num=0;
		if(!start || !finish){return ;}
		var cal=list.gantt.data.getCalendar(),startTime=start,lastDat=-1;
		while(startTime<finish)
		{
			var time=cal.getWorkTime(startTime);
			var day=[parseInt(time[0]/1000/60/60/24),parseInt(time[1]/1000/60/60/24)];
			num+=day[1]-day[0]+1;
			if(lastDat==day[0]){num--;}
			lastDat=day[1];
			startTime=time[1];
		}
		cell.appendChild(cell.ownerDocument.createTextNode(_OBS_10(list.gantt.config.getConfig("SFGanttField/durationFormat"),num)));
	}});
	 function SFGanttFieldSelecter()
	{
		SFGanttField.apply(this,arguments);
		this.inputData=this.bodyData;
	}
	SFGanttFieldSelecter.prototype=new SFGanttField()
	_OBS_4(SFGanttFieldSelecter.prototype,{_getOptions: function(element,list)
	{
		var options=this.getOptions(element,list);
		if(options)
		{
			if(!options.length)
			{
				var item,opts=options;
				options=[];
				for(item in opts)
				{
					if(typeof(opts[item])==_OBS_1[181])
					{
						options.push([item,opts[item]]);
					}
				}
			}
		}
		return options;
	},getOptions: function()
	{
		return this.options;
	},bodyFunc: function(cell,element,list)
	{
		var inputData=this.inputData,field=this,options=this._getOptions(element,list),doc=cell.ownerDocument;
		var value=element[inputData];
		for(var i=0;i<options.length;i++)
		{
			if(typeof(options[i])==_OBS_1[184] && options[i].length>1 && options[i][0]==value)
			{
				cell.appendChild(doc.createTextNode(options[i][1]));
				return;
			}
			if(typeof(options[i])!=_OBS_1[184] && i==value)
			{
				cell.appendChild(doc.createTextNode(options[i]));
				return;
			}
		}
		cell.appendChild(doc.createTextNode((typeof(value)!=_OBS_1[174])?value:""));
	},inputFunc: function(cell,element,list)
	{
		var inputData=this.inputData,field=this,options=this._getOptions(element,list);
		var value=element[inputData];
		var input=cell.ownerDocument.createElement("select");
		_OBS_31(input,_OBS_1[73],_OBS_27);
		_OBS_31(input,_OBS_1[170],_OBS_27);
		_OBS_31(input,_OBS_1[172],function(e)
		{
			_OBS_32(input.cml);
			input.cml=_OBS_31(input,_OBS_1[52],_OBS_27);
			_OBS_27(e);
		});
		_OBS_31(input,_OBS_1[24],_OBS_27);
		input.cml=_OBS_31(input,_OBS_1[52],_OBS_26);
		for(var i=0;i<options.length;i++)
		{
			var oi=options[i];
			if(typeof(oi)!=_OBS_1[184]){oi=[i,oi];}
			input.options.add(new Option(oi[1],oi[0]));
		}
		input.value=(typeof(value)!=_OBS_1[174])?value:"";
		_OBS_31(input,_OBS_1[141],function()
		{
			element.setProperty(inputData,input.value);
		});
		cell.appendChild(input);
		input.focus();
	}});
	 function SFGanttFieldTreeName()
	{
		SFGanttField.apply(this,arguments);
		this.bodyData="Name,Summary,Collapse";
		this.inputData='Name';
	}
	SFGanttFieldTreeName.prototype=new SFGanttField()
	_OBS_4(SFGanttFieldTreeName.prototype,{bodyFunc: function (cell,element,list)
	{
		var doc=cell.ownerDocument;
		if(list)
		{
			for(var p=element;p;p=p.getParent())
			{
				if(p==element && element.Summary){continue;}
				cell.appendChild(doc.createTextNode(_OBS_3("6Qmm")));
			}
		}
		if(element.Summary && list && !(list.gantt.hideSummary && list.gantt.inline))
		{
			var img=this.getCollapseImg(list.gantt,element.Collapse);
			cell.appendChild(img);
			_OBS_31(img,_OBS_1[73],function(e){_OBS_27(e);element.setProperty(_OBS_1[127],!element.Collapse);});
		}
		cell.appendChild(doc.createTextNode((element.Name?element.Name:"")));
	},getCollapseImg: function(gantt,collapse)
	{
		var img=gantt.createImage("collapse_"+(collapse?"close":"open"));
		_OBS_4(img.style,{margin:"1px",cursor:_OBS_1[183]});
		return img;
	}});
	 function SFGanttFieldIcon()
	{
		SFGanttField.apply(this,arguments);
		this.ReadOnly=true;
		this.bodyDatas=[];
		this.icons=[];
	}
	SFGanttFieldIcon.prototype=new SFGanttField()
	_OBS_4(SFGanttFieldIcon.prototype,{headFunc: function(cell,list)
	{
		var img=list.gantt.createImage("icon_taskstatus");
		_OBS_4(img.style,{position:_OBS_1[178]});
		cell.appendChild(img);
	},bodyFunc: function(cell,element,list)
	{
		cell.vAlign="middle";
		var img;
		for(var i=0;i<this.icons.length;i++)
		{
			if(img=this.icons[i].showHandle.apply(this,[element,list.gantt]))
			{
				cell.appendChild(img);
			}
		}
	},createImage: function(gantt,name)
	{
		var img=gantt.createImage(name);
		_OBS_4(img.style,{position:_OBS_1[178]});
		return img;
	},addIcon: function(showHandle,data)
	{
		if(data)
		{
			var datas=data.split(",");
			for(var i=datas.length-1;i>=0;i--){if(!_OBS_13(this.bodyDatas,datas[i])){this.bodyDatas.push(datas[i]);}}
			this.bodyData=this.bodyDatas.join(",");
		}
		this.icons.push({showHandle:showHandle});
	}});
	 function SFGanttMapItem()
	{
	}
	_OBS_4(SFGanttMapItem.prototype,{initialize: function(){return false;},show: function(){},onScale: function(){},onUpdate: function(){},onMouseDown: function(){},getTooltip: function(){},remove: function(){},depose: function(){}});
	 function SFGanttMapMilestoneHead()
	{
	}
	SFGanttMapMilestoneHead.prototype=new SFGanttMapItem()
	_OBS_4(SFGanttMapMilestoneHead.prototype,{initialize: function(control)
	{
		_OBS_4(this,{control:control,name:'MilestoneHead'});
		return true;
	},show: function(task,mapObj)
	{
		var start=task.Start.valueOf(),finish=task.Finish.valueOf();
		if(start!=finish){return;}
		var control=this.control,gantt=control.gantt;
		var img=mapObj[this.name]=gantt.createImage(_OBS_1[20],{position:[(-Math.floor((control.taskHeight-1)/2)),Math.ceil(control.taskPadding/2)],size:[(control.taskHeight-1),11]});
		_OBS_4(img.style,{position:_OBS_1[179],zIndex:150});
		var taskStyle=control.getTaskStyle(task);
		if(taskStyle.milestoneImage){imgSrc=taskStyle.milestoneImage;}
		_OBS_54(img,imgSrc);
		mapObj.taskDiv.appendChild(img);
	},getTooltip: function(task,mapObj,tooltip,e)
	{
		if(e.target!=mapObj[this.name] || !this.control.taskNoticeFields){return false;}
		var control=this.control;
		if(tooltip.bindObject==task && tooltip.bindType==_OBS_1[160]){return false;}
		var table=control.getTaskTooltipContent(task,control.tooltipTitle.milestone,control.taskNoticeFields.split(","));
		tooltip.bindObject=task;
		tooltip.bindType=_OBS_1[160];
		tooltip.setContent(table);
		return true;
	},onUpdate: function(task,mapObj,changedFields)
	{
		var gantt=this.control.gantt,start=task.Start.valueOf(),finish=task.Finish.valueOf();
		if(start!=finish){this.remove(task,mapObj);return;}
		if(_OBS_13(changedFields,_OBS_1[128]))
		{
			this.remove(task,mapObj);
			this.show(task,mapObj);
			return;
		}
		var div=mapObj[this.name];
		if(!div)
		{
			this.show(task,mapObj);
		}
	},remove: function(task,mapObj)
	{
		_OBS_29(mapObj[this.name]);
		delete mapObj[this.name];
	}});
	 function SFGanttMapSummaryHead()
	{
	}
	SFGanttMapSummaryHead.prototype=new SFGanttMapItem()
	_OBS_4(SFGanttMapSummaryHead.prototype,{initialize: function(control)
	{
		_OBS_4(this,{control:control,name:'SummaryHead'});
		return true;
	},show: function(task,mapObj,scale)
	{
		var start=task.Start.valueOf(),finish=task.Finish.valueOf(),doc=mapObj.taskDiv.ownerDocument;
		if(start==finish || !task.Summary){return;}
		var control=this.control,gantt=control.gantt,imgs=mapObj[this.name]=[],imgSrc=_OBS_1[19];
		scale=scale?scale:gantt.getScale();
		var taskStyle=control.getTaskStyle(task);
		if(taskStyle.summaryImage){imgSrc=taskStyle.summaryImage;}
		for(var i=0;i<2;i++)
		{
			var left=-Math.floor((control.taskHeight-1)/2);
			if(i>0){left+=(finish-start)/scale;}
			var img=gantt.createImage(imgSrc,{position:[left,Math.ceil(control.taskPadding/2)],size:[(control.taskHeight-1),11]});
			imgs.push(img);
			_OBS_4(img.style,{position:_OBS_1[179],zIndex:150});
			mapObj.taskDiv.appendChild(img);
		}
	},onUpdate: function(task,mapObj,changedFields)
	{
		var gantt=this.control.gantt,start=task.Start.valueOf(),finish=task.Finish.valueOf(),control=this.control;
		if(start==finish || !task.Summary){this.remove(task,mapObj);return;}
		var div=mapObj[this.name];
		if(!div)
		{
			this.show(task,mapObj);
		}
		else
		{
			if(_OBS_13(changedFields,_OBS_1[139]) || _OBS_13(changedFields,_OBS_1[140])){mapObj[this.name][1].style.left=(-Math.floor((control.taskHeight-1)/2)+(finish-start)/gantt.getScale())+"px"}
		}
	},onScale: function(task,mapObj,scale)
	{
		var div=mapObj[this.name];
		if(div){div[1].style.left=(-Math.floor((this.control.taskHeight-1)/2)+(task.Finish-task.Start)/scale)+"px";}
	},remove: function(task,mapObj)
	{
		var imgs=mapObj[this.name];
		if(imgs)
		{
			_OBS_29(imgs[0]);
			_OBS_29(imgs[1]);
		}
		delete mapObj[this.name];
	}});
	 function SFGanttMapBarSummary()
	{
	}
	SFGanttMapBarSummary.prototype=new SFGanttMapItem()
	_OBS_4(SFGanttMapBarSummary.prototype,{initialize: function(control)
	{
		_OBS_4(this,{control:control,name:'BarSummary'});
		return true;
	},show: function(task,mapObj,scale)
	{
		var start=task.Start.valueOf(),finish=task.Finish.valueOf();
		if(start>=finish || !task.Summary){return;}
		var control=this.control,gantt=control.gantt,div=mapObj.taskDiv.ownerDocument.createElement("div");
		scale=scale?scale:gantt.getScale();
		mapObj[this.name]=div;
		div.style.cssText=_OBS_1[18]+((finish-start)/scale)+_OBS_1[17]+Math.ceil(control.taskPadding/2)+_OBS_1[34]+Math.floor(control.taskHeight/2-1)+"px;";
		var taskStyle=control.getTaskStyle(task);
		if(taskStyle.summaryBarStyle){_OBS_4(div.style,taskStyle.summaryBarStyle);}
		mapObj.taskDiv.appendChild(div);
	},getTooltip: function(task,mapObj,tooltip,e)
	{
		if(e.target!=mapObj[this.name] || !this.control.taskNoticeFields){return false;}
		var control=this.control;
		if(tooltip && tooltip.bindObject==task && tooltip.bindType==_OBS_1[160]){return false;}
		var table=control.getTaskTooltipContent(task,control.tooltipTitle.summary,control.taskNoticeFields.split(","));
		tooltip.bindObject=task;
		tooltip.bindType=_OBS_1[160];
		tooltip.setContent(table);
		return true;
	},onUpdate: function(task,mapObj,changedFields)
	{
		var gantt=this.control.gantt,start=task.Start.valueOf(),finish=task.Finish.valueOf();
		if(start>=finish || !task.Summary){this.remove(task,mapObj);return;}
		var div=mapObj[this.name];
		if(!div)
		{
			this.show(task,mapObj,gantt.getScale());
		}
		else
		{
			var style=div.style;
			if(_OBS_13(changedFields,_OBS_1[139]) || _OBS_13(changedFields,_OBS_1[140])){style.width=((finish-start)/gantt.getScale())+"px";}
		}
	},onScale: function(task,mapObj,scale)
	{
		var div=mapObj[this.name];
		if(div){div.style.width=((task.Finish-task.Start)/scale)+"px";}
	},remove: function(task,mapObj)
	{
		_OBS_29(mapObj[this.name]);
		delete mapObj[this.name];
	}});
	 function SFGanttMapBarNormal()
	{
	}
	SFGanttMapBarNormal.prototype=new SFGanttMapItem()
	_OBS_4(SFGanttMapBarNormal.prototype,{initialize: function(control)
	{
		_OBS_4(this,{control:control,name:'BarNormal'});
		return true;
	},show: function(task,mapObj,scale)
	{
		var start=task.Start.valueOf(),finish=task.Finish.valueOf();
		if(start>=finish || task.Summary){return;}
		var control=this.control,gantt=control.gantt,div=mapObj.taskDiv.ownerDocument.createElement("div");
		scale=scale?scale:gantt.getScale();
		mapObj[this.name]=div;
		var height=control.taskHeight;
		if(gantt.isTracking){height=height/2;}
		div.style.cssText=_OBS_1[18]+((finish-start)/scale)+_OBS_1[17]+Math.ceil(control.taskPadding/2)+_OBS_1[34]+height+"px;cursor:move;";
		var taskStyle=control.getTaskStyle(task),bStyle=taskStyle.barStyle;
		if(bStyle)
		{
			if(bStyle.bgImage)
			{
				gantt.setBackgroundImage(div,bStyle.bgImage,{color:bStyle.bgColor});
			}
			_OBS_4(div.style,bStyle);
		}
		mapObj.taskDiv.appendChild(div);
	},onUpdate: function(task,mapObj,changedFields)
	{
		var gantt=this.control.gantt,start=task.Start.valueOf(),finish=task.Finish.valueOf();
		if(start>=finish || task.Summary){this.remove(task,mapObj);return;}
		var div=mapObj[this.name];
		if(!div)
		{
			this.show(task,mapObj);
		}
		else
		{
			if(_OBS_13(changedFields,_OBS_1[128]))
			{
				this.remove(task,mapObj);
				this.show(task,mapObj);
				return;
			}
			var style=div.style;
			if(_OBS_13(changedFields,_OBS_1[139]) || _OBS_13(changedFields,_OBS_1[140]))
			{
				div.style.left='0px';
				style.width=((finish-start)/gantt.getScale())+"px";
			}
		}
	},getTooltip: function(task,mapObj,tooltip,e)
	{
		if(e.target!=mapObj[this.name] || !this.control.taskNoticeFields){return false;}
		var control=this.control;
		if(tooltip && tooltip.bindObject==task && tooltip.bindType==_OBS_1[160]){return false;}
		var table=control.getTaskTooltipContent(task,control.tooltipTitle.task,control.taskNoticeFields.split(","));
		tooltip.bindObject=task;
		tooltip.bindType=_OBS_1[160];
		tooltip.setContent(table);
		return true;
	},onScale: function(task,mapObj,scale)
	{
		var div=mapObj[this.name];
		if(div){div.style.width=((task.Finish-task.Start)/scale)+"px";}
	},onMouseDown: function(task,mapObj,e)
	{
		if(e.target!=mapObj[this.name]){return;}
		
		_OBS_35(mapObj[this.name],_OBS_1[73],[]);
		new SFDragObject(this.control.div,_OBS_22(this,this.onMove),{interval:32}).onMouseDown(e);
	},onMove: function(sp,lp,type)
	{
		var control=this.control,gantt=control.gantt,task=control.dragTask,mapObj=gantt.getElementDrawObj(task)[control.proTag];
		var span=[lp[0]-sp[0],lp[1]-sp[1]]
		if(!control.dragType)
		{
			if(Math.sqrt(Math.pow(span[0],2)+Math.pow(span[1],2))<5){return;}
			if(span[0]==0 || span[1]/span[0]>2 && !control.gantt.readOnly && control.gantt.data.canAddLink(task) && !control.gantt.disableAddLink && !control.disableDragAddLink)
			{
				control.dragType=_OBS_1[158];
				control.startHeight=mapObj.taskDiv.offsetTop;
				
				var link={Type:1,PredecessorTask:task};
				control.dragLink=link;
				if(gantt.getTooltip)
				{
					var tooltip=gantt.getTooltip();
					gantt.getTooltip().setEnable(false);
					tooltip.setContent(control.getLinkTooltipContent(link));
					tooltip.show([0,0]);
				}
				mapObj[this.name].style.borderStyle=_OBS_1[163];
			}
			else if((span[1]==0 || span[0]/span[1]>2) && !gantt.readOnly && task.canSetProperty(_OBS_1[139]) && !control.gantt.disableUpdateTask && !control.disableDragMoveTask)
			{
				control.dragType=_OBS_1[139];
				if(gantt.getTooltip)
				{
					var tooltip=gantt.getTooltip();
					tooltip.setContent(control.getTaskTooltipContent(task,control.tooltipTitle.task,[_OBS_1[139],_OBS_1[140]]));
					gantt.getTooltip().setEnable(false);
					var position=_OBS_36(mapObj.taskDiv,gantt.container);
					position[1]+=gantt.getElementDrawObj(task).height;
					tooltip.show(position);
				}
				
				_OBS_35(gantt,"taskbardragstart",[task]);
			}
			else{return;}
		}
		if(control.dragType==_OBS_1[139])
		{
			var offset=span[0]*gantt.getScale();
			var start=new Date(task.Start.valueOf()+offset),finish=new Date(task.Finish.valueOf()+offset);
			if(type!="end")
			{
				
				var left=lp[0]+gantt.getMapPanel().offsetLeft;
				if(left<=0 || left>gantt.getLayout(_OBS_1[85]).offsetWidth)
				{
					this.dmDir=(left<=0)?-1:1;
					this.lastOffset=span[0];
					if(!this.dmt){this.dmt=window.setInterval(_OBS_22(this,this.onTime),32);}
				}
				else
				{
					if(this.dmt)
					{
						window.clearInterval(this.dmt);
						delete this.dmt;
					}
				}
				
				mapObj[this.name].style.left=gantt.getMapPanelPosition(start)-gantt.getTimePanelPosition(task.Start)+"px";
				if(gantt.getTooltip)
				{
					gantt.getTooltip().setContent(control.getTaskTooltipContent({Start:start,Finish:finish},control.tooltipTitle.task,[_OBS_1[139],_OBS_1[140]]));
					gantt.getTooltip().setEnable(false);
				}
			}
			else
			{
				if(this.dmt)
				{
					window.clearInterval(this.dmt);
					delete this.dmt;
				}
				if(task.canSetProperty(_OBS_1[140],finish) && task.canSetProperty(_OBS_1[139],start))
				{
					task.setProperty(_OBS_1[140],finish);
					task.setProperty(_OBS_1[139],start);
					
				}
				else
				{
					mapObj[this.name].style.left='0px';
				}
				_OBS_35(gantt,"taskbardragend",[task]);
				if(gantt.getTooltip){gantt.getTooltip().setEnable(true);}
				delete control.dragType
			}
		}
		else
		{
			
			if(control.dragFlagLine){_OBS_29(control.dragFlagLine);}
			if(type!="end")
			{
				var offset=_OBS_36(mapObj.taskDiv,control.div);
				var points=[];
				points.push([sp[0],sp[1]]);
				points.push([lp[0],lp[1]]);
				
				var minX=Number.MAX_VALUE,minY=Number.MAX_VALUE,maxX=0,maxY=0;
				for(var i=0;i<points.length;i++)
				{
					minX=Math.min(minX,points[i][0])
					minY=Math.min(minY,points[i][1])
					maxX=Math.max(maxX,points[i][0])
					maxY=Math.max(maxY,points[i][1])
				}
				var graphics=this.getGraphics();
				control.div.appendChild(graphics.div);
				graphics.setLineColor(_OBS_1[58])
				graphics.setLineWeight(1);
				graphics.setPosition({x:minX,y:minY});
				graphics.start({x:0,y:0},1,{x:maxX-minX,y:maxY-minY});
				graphics.moveTo({x:points[0][0]-minX,y:points[0][1]-minY});
				for(var i=1;i<points.length;i++)
				{
					graphics.lineTo({x:points[i][0]-minX,y:points[i][1]-minY});
				}
				graphics.finish();
				control.dragFlagLine=graphics.div;
				
				var distance=lp[1]-control.startHeight;
				var t=task;
				if(distance<0){t=t.getPreviousViewTask();}
					
				while(t)
				{
					var nextDis=distance+(distance<0?1:-1)*gantt.getElementHeight(t);
					if(distance*nextDis<=0){break;}
					t=distance>0?t.getNextViewTask():t.getPreviousViewTask();
					distance=nextDis;
				}
					
				var eTime=gantt.getTimeByMapPanelPosition(lp[0]);
				while(t)
				{
					if(t.Start<=eTime && eTime<=t.Finish){break;}
					t=distance>0?t.getNextViewTask():t.getPreviousViewTask();
					if(gantt.getElementHeight(t)>0){t=null;}
				}
				if(t==task){t=null;}
				
				if(t)
				{
					var objSpan=gantt.getElementDrawObj(t)[control.proTag].taskDiv;
					var objOffset=_OBS_36(objSpan,control.div);
					if(lp[0]<objOffset[0]-10 || lp[0]>objOffset[0]+objSpan.offsetWidth+10){t=null;}
				}
				var lastLinkTask=control.dragLink.SuccessorTask,linkTaskMapObj;
				
				if(lastLinkTask!=t)
				{
					if(t)
					{
						linkTaskMapObj=gantt.getElementDrawObj(t)[control.proTag];
						if(linkTaskMapObj && linkTaskMapObj[this.name])
						{
							linkTaskMapObj[this.name].style.borderStyle=_OBS_1[163];
						}
					}
					if(lastLinkTask)
					{
						linkTaskMapObj=gantt.getElementDrawObj(lastLinkTask)[control.proTag];
						if(linkTaskMapObj && linkTaskMapObj[this.name])
						{
							linkTaskMapObj[this.name].style.borderStyle=_OBS_1[167];
						}
					}
					control.dragLink.SuccessorTask=t;
					if(gantt.getTooltip)
					{
						gantt.getTooltip().setContent(control.getLinkTooltipContent(control.dragLink));
						gantt.getTooltip().setEnable(false);
					}
				}
			}
			else
			{
				
				var lastLinkTask=control.dragLink.SuccessorTask;
				if(control.dragLink && lastLinkTask)
				{
					lastLinkTask.addPredecessorLink(task,1);
				}
				
				if(lastLinkTask)
				{
					var linkTaskMapObj=gantt.getElementDrawObj(lastLinkTask)[control.proTag];
					if(linkTaskMapObj && linkTaskMapObj[this.name])
					{
						linkTaskMapObj[this.name].style.borderStyle=_OBS_1[167];
					}
				}
				
				mapObj[this.name].style.borderStyle=_OBS_1[167];
				if(gantt.getTooltip){gantt.getTooltip().setEnable(true);}
				delete control.dragType;
				delete control.dragTask;
			}
		}
	},onTime: function()
	{
		var control=this.control,gantt=control.gantt,task=control.dragTask,mapObj=gantt.getElementDrawObj(task)[control.proTag];
		gantt.setStartTime(new Date(gantt.getStartTime().valueOf()+gantt.getScale()*6*this.dmDir));
		this.lastOffset+=6*this.dmDir
		var start=new Date(task.Start.valueOf()+this.lastOffset*gantt.getScale());
		mapObj[this.name].style.left=gantt.getMapPanelPosition(start)-gantt.getTimePanelPosition(task.Start)+"px";
	},getGraphics: function()
	{
		var graphics=[SFGraphicsSvg,SFGraphicsVml,SFGraphicsCanvas,SFGraphicsDiv];
		for(var i=0;i<graphics.length;i++)
		{
			if(graphics[i].isSupport())
			{
				return new graphics[i]();
			}
		}
		return new SFGraphics(true);
	},remove: function(task,mapObj)
	{
		_OBS_29(mapObj[this.name]);
		delete mapObj[this.name];
	}});
	 function SFGanttMapText()
	{
	}
	SFGanttMapText.prototype=new SFGanttMapItem()
	_OBS_4(SFGanttMapText.prototype,{initialize: function(control)
	{
		
		var fields=this.fields={},needText=false,fNames=["Center","Top","Bottom"],fieldStr;
		
		if(!control.gantt.inline)
		{
			fNames=fNames.concat("Left","Right");
			if((fieldStr=control.taskBarField))
			{
				fields["Right"]=_OBS_89(fieldStr);
				if(!needText){needText=true;}
			}
		}
		for(var i=0;i<fNames.length;i++)
		{
			if((fieldStr=control["taskBar"+fNames[i]+"Field"]))
			{
				fields[fNames[i]]=_OBS_89(fieldStr);
				if(!needText){needText=true;}
			}
		}
		if(!needText){return false;}
		_OBS_4(this,{control:control,name:'Text'});
		return true;
	},getStyle: function(task,scale,key)
	{
		if(!task.Start || !task.Finish){return}
		var start=task.Start.valueOf(),finish=task.Finish.valueOf(),gantt=this.control.gantt;
		var left=0,width=Math.max((finish-start)/scale,1),top=Math.ceil((gantt.itemHeight-gantt.fontSize)/2),align=_OBS_1[95],overflow=_OBS_1[180];
		switch(key)
		{
			case "Left":
				width=1000;
				left=-1010;
				align=_OBS_1[59];
				break;
			case "Right":
				width=0;
				left=(finish-start)/scale+10
				overflow="visible";
				break;
			case "Top":
				top-=Math.max(gantt.fontSize,gantt.itemHeight/4)+2;
				align=_OBS_1[81];
				break;
			case "Bottom":
				top+=Math.max(gantt.fontSize,gantt.itemHeight/4)+2;
				align=_OBS_1[81];
				break;
			case "Center":
				align=_OBS_1[81];
				break;
			default:return;
		}
		return {left:left+"px",top:top+"px",width:width?(width+"px"):"auto",textAlign:align,overflow:overflow}
	},show: function(task,mapObj,scale)
	{
		var control=this.control,gantt=control.gantt,height=gantt.getElementDrawObj(task).height,fields=this.fields;
		scale=scale?scale:gantt.getScale();
		for(var key in fields)
		{
			var style=this.getStyle(task,scale,key);
			if(!style){continue;}
			div=mapObj.taskDiv.ownerDocument.createElement("div")
			div.noWrap=true;
			mapObj[this.name+key]=div;
			fields[key].showBody(div,task,control);
			div.style.cssText="position:absolute;white-space:nowrap;z-index:200;cursor:default;font-weight:bolder;font-size:"+gantt.fontSize+"px;";
			_OBS_4(div.style,style);
			mapObj.taskDiv.appendChild(div);
		}
	},onUpdate: function(task,mapObj,changedFields)
	{
		var control=this.control,gantt=control.gantt,scale=scale?scale:gantt.getScale();
		var fields=this.fields;
		for(var key in fields)
		{
			var style=this.getStyle(task,scale,key);
			if(!style){continue;}
			var div=mapObj[this.name+key];
			if(!div)
			{
				this.show(task,mapObj);
				return;
			}
			if(_OBS_13(changedFields,_OBS_1[139]) || _OBS_13(changedFields,_OBS_1[140]))
			{
				_OBS_4(div.style,style);
			}
			if(fields[key].checkUpdate(changedFields))
			{
				fields[key].showBody(div,task,control);
			}
		}
	},onScale: function(task,mapObj,scale)
	{
		var fields=this.fields;
		for(var key in fields)
		{
			var style=this.getStyle(task,scale,key);
			if(!style){continue;}
			var div=mapObj[this.name+key];
			if(!div){continue;}
			_OBS_4(div.style,style);
		}
	},remove: function(task,mapObj)
	{
		var fields=this.fields;
		for(var key in fields)
		{
			_OBS_29(mapObj[this.name+key]);
			delete mapObj[this.name+key];
		}
	}});
	 function SFGanttMapResize()
	{
	}
	SFGanttMapResize.prototype=new SFGanttMapItem()
	_OBS_4(SFGanttMapResize.prototype,{initialize: function(control)
	{
		if(control.gantt.readOnly || control.gantt.disableUpdateTask || control.disableDragResizeTask){return false;}
		_OBS_4(this,{control:control,name:'Resize'});
		return true;
	},show: function(task,mapObj,scale)
	{
		var start=task.Start.valueOf(),finish=task.Finish.valueOf(),control=this.control,gantt=control.gantt,height=gantt.getElementDrawObj(task).height;
		scale=scale?scale:gantt.getScale();
		if(start>=finish || task.Summary || gantt.readOnly || !task.canSetProperty(_OBS_1[140])){return;}
		var div=mapObj.taskDiv.ownerDocument.createElement("div");
		mapObj[this.name]=div;
		div.style.cssText=_OBS_1[16]+(control.taskHeight-1)+"px;left:"+((finish-start)/scale-Math.floor((control.taskHeight-1)/2))+_OBS_1[17]+Math.ceil(control.taskPadding/2)+_OBS_1[34]+(height/2+2)+"px;z-index:150;font-size:0px;cursor:w-resize;";
		mapObj.taskDiv.appendChild(div);
	},onUpdate: function(task,mapObj,changedFields)
	{
		var start=task.Start.valueOf(),finish=task.Finish.valueOf(),control=this.control,gantt=control.gantt,height=gantt.getElementHeight(task);
		if(start>=finish || task.Summary || gantt.readOnly || !task.canSetProperty(_OBS_1[140]) || gantt.disableUpdateTask || control.disableDragResizeTask){this.remove(task,mapObj);return;}
		var div=mapObj[this.name];
		if(!div)
		{
			this.show(task,mapObj);
		}
		else
		{
			var style=div.style;
			if(_OBS_13(changedFields,_OBS_1[139]) || _OBS_13(changedFields,_OBS_1[140])){style.left=((finish-start)/gantt.getScale()-Math.floor((control.taskHeight-1)/2))+"px";}
		}
	},onScale: function(task,mapObj,scale)
	{
		var div=mapObj[this.name];
		if(div){div.style.left=((task.Finish-task.Start)/scale)+"px";}
	},onMouseDown: function(task,mapObj,e)
	{
		if(e.target!=mapObj[this.name]){return;}
		var control=this.control;
		new SFDragObject(control.div,_OBS_22(this,this.onResizeMove)).onMouseDown(e);
	},onResizeMove: function(sp,lp,type)
	{
		var control=this.control,gantt=control.gantt,task=control.dragTask,barDiv=gantt.getElementDrawObj(task)[control.proTag].BarNormal,scale=gantt.getScale();
		if(type==_OBS_1[169])
		{
			if(gantt.getTooltip)
			{
				var tooltip=gantt.getTooltip();
				tooltip.setContent(control.getTaskTooltipContent(task,control.tooltipTitle.task,[_OBS_1[139],_OBS_1[140]]));
				var position=_OBS_36(barDiv,gantt.container);
				position[1]+=gantt.getElementDrawObj(task).height;
				tooltip.show(position);
			}
		}
		var finish=task.Finish.valueOf()+[lp[0]-sp[0]]*scale;
		finish=Math.max(task.Start.valueOf(),finish)
		if(type!="end")
		{
			barDiv.style.width=(finish-task.Start.valueOf())/scale+"px";
			if(gantt.getTooltip)
			{
				gantt.getTooltip().setContent(control.getTaskTooltipContent({Start:task.Start,Finish:new Date(finish)},control.tooltipTitle.task,[_OBS_1[139],_OBS_1[140]]))
			}
		}
		else
		{
			if(!task.setProperty(_OBS_1[140],new Date(finish)))
			{
				barDiv.style.width=(task.Finish.valueOf()-task.Start.valueOf())/scale+"px";
			}
		}
	},remove: function(task,mapObj)
	{
		_OBS_29(mapObj[this.name]);
		delete mapObj[this.name];
	}});
	 function SFGanttMapPercentChange()
	{
	}
	SFGanttMapPercentChange.prototype=new SFGanttMapItem()
	_OBS_4(SFGanttMapPercentChange.prototype,{initialize: function(control)
	{
		if(control.gantt.readOnly || control.gantt.disableUpdateTask || control.disableDragChangePercent){return false;}
		_OBS_4(this,{control:control,name:'PercentChange'});
		return true;
	},show: function(task,mapObj)
	{
		var start=task.Start.valueOf(),finish=task.Finish.valueOf(),percent=task.PercentComplete,control=this.control,gantt=control.gantt,height=control.taskHeight;
		if(start>=finish || task.Summary || gantt.readOnly || !task.canSetProperty(_OBS_1[134])){return;}
		percent=percent?percent:0;
		var left=(finish-start)/gantt.getScale()*percent/100;
		var div=mapObj.taskDiv.ownerDocument.createElement("div");
		mapObj[this.name]=div;
		div.style.cssText=_OBS_1[16]+(Math.floor((control.taskHeight-1)/2))+"px;background-color:#FFFFFF;left:"+left+_OBS_1[17]+Math.ceil(control.taskPadding/2)+_OBS_1[34]+(height+2)+"px;z-index:250;font-size:0px;cursor:col-resize;";
		mapObj.taskDiv.appendChild(div);
		_OBS_12(div,0.01);
	},onUpdate: function(task,mapObj,changedFields)
	{
		var start=task.Start.valueOf(),finish=task.Finish.valueOf(),percent=task.PercentComplete,control=this.control,gantt=control.gantt;
		if(start>=finish || task.Summary || gantt.readOnly || !task.canSetProperty(_OBS_1[134]) || gantt.disableUpdateTask || control.disableDragChangePercent){this.remove(task,mapObj);return;}
		var div=mapObj[this.name];
		if(!div)
		{
			this.show(task,mapObj);
		}
		else
		{
			var style=div.style,percent=percent?percent:0;
			if(_OBS_13(changedFields,_OBS_1[134]) || _OBS_13(changedFields,_OBS_1[139]) || _OBS_13(changedFields,_OBS_1[140]))
			{
				style.left=(finish-start)/gantt.getScale()*percent/100+"px";
			}
		}
	},onScale: function(task,mapObj,scale)
	{
		var div=mapObj[this.name];
		var percent=task.PercentComplete;
		percent=percent?percent:0;
		if(div){div.style.left=(task.Finish-task.Start)/this.control.gantt.getScale()*percent/100+"px";}
	},onMouseDown: function(task,mapObj,e)
	{
		if(e.target!=mapObj[this.name]){return;}
		var control=this.control;
		new SFDragObject(control.div,_OBS_22(this,this.onPercentMove)).onMouseDown(e);
	},onPercentMove: function(sp,lp,type)
	{
		var control=this.control,gantt=control.gantt,task=control.dragTask,mapObj=gantt.getElementDrawObj(control.dragTask)[control.proTag],percentDiv=mapObj.Percent;
		if(!percentDiv){return;}
		var percent=task.PercentComplete,start=task.Start,finish=task.Finish,size=(finish-start)/gantt.getScale();
		if(!percent){percent=0;}
		if(type==_OBS_1[169])
		{
			if(gantt.getTooltip)
			{
				var tooltip=gantt.getTooltip();
				tooltip.setContent(control.getTaskTooltipContent(task,control.tooltipTitle.progress,["name",_OBS_1[134]]));
				var position=_OBS_36(mapObj.BarNormal,gantt.container);
				position[1]+=gantt.getElementDrawObj(task).height;
				tooltip.show(position);
			}
		}
		if(type!="end")
		{
			percent=Math.round(percent+(lp[0]-sp[0])*100/size);
			percent=Math.min(Math.max(0,percent),100);
			percentDiv.style.width=(finish-start)/gantt.getScale()*percent/100+"px";
			if(gantt.getTooltip)
			{
				gantt.getTooltip().setContent(control.getTaskTooltipContent({PercentComplete:percent,Name:task.Name},control.tooltipTitle.progress,["name",_OBS_1[134]]))
			}
		}
		else
		{
			var p=parseInt(percent+(lp[0]-sp[0])*100/size);
			p=Math.min(Math.max(0,p),100);
			if(!task.setProperty(_OBS_1[134],p))
			{
				percent=task.getProperty(_OBS_1[134]);
				if(!percent){percent=0;}
				percentDiv.style.width=(finish-start)/gantt.getScale()*percent/100+"px";
			}
		}
	},remove: function(task,mapObj)
	{
		_OBS_29(mapObj[this.name]);
		delete mapObj[this.name];
	}});
	 function SFGanttMapPercent()
	{
	}
	SFGanttMapPercent.prototype=new SFGanttMapItem()
	_OBS_4(SFGanttMapPercent.prototype,{initialize: function(control)
	{
		_OBS_4(this,{control:control,name:_OBS_1[15]});
		return true;
	},show: function(task,mapObj)
	{
		var start=task.Start.valueOf(),finish=task.Finish.valueOf();
		if(start>=finish || task.Summary){return;}
		var control=this.control,gantt=control.gantt,div=mapObj.taskDiv.ownerDocument.createElement("div");
		var height=control.taskHeight,percent=task.PercentComplete;
		if(gantt.isTracking){height=Math.floor(height/2);}
		percent=percent?percent:0;
		percent=Math.max(0,Math.min(percent,100));
		var width=(finish-start)/gantt.getScale()*percent/100;
		div.style.cssText="position:absolute;font-size:0px;z-index:200;left:0px;width:"+width+_OBS_1[17]+Math.ceil(control.taskPadding/2+height/4+1)+_OBS_1[34]+(height/2)+"px;";
		var taskStyle=control.getTaskStyle(task);
		if(taskStyle.percentBarStyle){_OBS_4(div.style,taskStyle.percentBarStyle);}
		mapObj.taskDiv.appendChild(div);
		mapObj[this.name]=div;
	},getTooltip: function(task,mapObj,tooltip,e)
	{
		if(e.target!=mapObj[this.name] || !this.control.taskProgressNoticeFields){return false;}
		var control=this.control;
		if(tooltip && tooltip.bindObject==task && tooltip.bindType==_OBS_1[15]){return false;}
		var table=control.getTaskTooltipContent(task,control.tooltipTitle.progress,control.taskProgressNoticeFields.split(","));
		tooltip.bindObject=task;
		tooltip.bindType=_OBS_1[15];
		tooltip.setContent(table);
		return true;
	},onUpdate: function(task,mapObj,changedFields)
	{
		var start=task.Start.valueOf(),finish=task.Finish.valueOf();
		if(start>=finish || task.Summary){this.remove(task,mapObj);return;}
		var div=mapObj[this.name];
		if(!div)
		{
			this.show(task,mapObj);
		}
		else
		{
			var style=div.style;
			if(_OBS_13(changedFields,_OBS_1[134]) || _OBS_13(changedFields,_OBS_1[139]) || _OBS_13(changedFields,_OBS_1[140]))
			{
				var percent=task.PercentComplete;
				percent=percent?percent:0;
				style.width=(finish-start)/this.control.gantt.getScale()*percent/100+"px";
			}
		}
	},onScale: function(task,mapObj,scale)
	{
		var div=mapObj[this.name];
		var percent=task.PercentComplete;
		percent=percent?percent:0;
		if(div){div.style.width=(task.Finish-task.Start)/this.control.gantt.getScale()*percent/100+"px";}
	},remove: function(task,mapObj)
	{
		_OBS_29(mapObj[this.name]);
		delete mapObj[this.name];
	}});
	 function SFGanttMapBarTrack()
	{
	}
	SFGanttMapBarTrack.prototype=new SFGanttMapItem()
	_OBS_4(SFGanttMapBarTrack.prototype,{initialize: function(control)
	{
		if(!control.gantt.isTracking){return false;}
		_OBS_4(this,{control:control,name:'BarTrack'});
		return true;
	},show: function(task,mapObj,scale)
	{
		if(!task.BaselineStart || !task.BaselineFinish){return;}
		var start=task.BaselineStart.valueOf(),finish=task.BaselineFinish.valueOf();
		if(start>=finish || task.Summary){return;}
		var control=this.control,gantt=control.gantt,div=mapObj.taskDiv.ownerDocument.createElement("div");
		scale=scale?scale:gantt.getScale();
		mapObj[this.name]=div;
		var height=control.taskHeight;
		div.style.cssText="position:absolute;font-size:0px;z-index:100;left:"+(start-task.Start.valueOf())/scale+"px;width:"+((finish-start)/scale)+_OBS_1[17]+(Math.ceil(control.taskPadding/2)+height/2)+_OBS_1[34]+height/2+"px;";
		var taskStyle=control.getTaskStyle(task),tbStyle=taskStyle.trackBarStyle;
		if(tbStyle)
		{
			if(tbStyle.bgImage)
			{
				gantt.setBackgroundImage(div,tbStyle.bgImage,{color:tbStyle.bgColor});
			}
			_OBS_4(div.style,tbStyle);
		}
		mapObj.taskDiv.appendChild(div);
	},onUpdate: function(task,mapObj,changedFields)
	{
		if(!task.BaselineStart || !task.BaselineFinish){return;}
		var gantt=this.control.gantt,start=task.BaselineStart,finish=task.BaselineFinish;
		if(!start || !finish || start.valueOf()>=finish.valueOf() || task.Summary){this.remove(task,mapObj);return;}
		var div=mapObj[this.name];
		if(!div)
		{
			this.show(task,mapObj);
		}
		else
		{
			if(_OBS_13(changedFields,_OBS_1[128]))
			{
				this.remove(task,mapObj);
				this.show(task,mapObj);
				return;
			}
			var style=div.style;
			if(_OBS_13(changedFields,_OBS_1[124]) || _OBS_13(changedFields,_OBS_1[123]))
			{
				style.width=((finish-start)/gantt.getScale())+"px";
			}
			if(_OBS_13(changedFields,_OBS_1[139]) || _OBS_13(changedFields,_OBS_1[124]))
			{
				style.left=((start-task.Start.valueOf())/gantt.getScale())+"px";
			}
		}
	},getTooltip: function(task,mapObj,tooltip,e)
	{
		if(e.target!=mapObj[this.name] || !this.control.taskNoticeFields){return false;}
		var control=this.control;
		if(tooltip && tooltip.bindObject==task && tooltip.bindType==_OBS_1[160]){return false;}
		var table=control.getTaskTooltipContent(task,control.tooltipTitle.task,control.taskTrackingNoticeFields.split(","));
		tooltip.bindObject=task;
		tooltip.bindType=_OBS_1[160];
		tooltip.setContent(table);
		return true;
	},onScale: function(task,mapObj,scale)
	{
		var div=mapObj[this.name];
		if(div)
		{
			div.style.width=((task.BaselineFinish-task.BaselineStart)/scale)+"px";
			div.style.left=((task.BaselineStart-task.Start.valueOf())/scale)+"px";
		}
	},remove: function(task,mapObj)
	{
		_OBS_29(mapObj[this.name]);
		delete mapObj[this.name];
	}});
	 function SFGanttMapMilestoneTrackHead()
	{
	}
	SFGanttMapMilestoneTrackHead.prototype=new SFGanttMapItem()
	_OBS_4(SFGanttMapMilestoneTrackHead.prototype,{initialize: function(control)
	{
		if(!control.gantt.isTracking){return false;}
		_OBS_4(this,{control:control,name:'MilestoneTrackHead'});
		return true;
	},show: function(task,mapObj)
	{
		if(!task.BaselineStart || !task.BaselineFinish){return;}
		var start=task.BaselineStart.valueOf(),finish=task.BaselineFinish.valueOf();
		if(start!=finish){return;}
		var control=this.control,gantt=control.gantt;
		var img=mapObj[this.name]=gantt.createImage(_OBS_1[14],{position:[(-Math.floor((control.taskHeight-1)/2)),Math.ceil(control.taskPadding/2)],size:[(control.taskHeight-1),11]});
		_OBS_4(img.style,{position:_OBS_1[179],zIndex:150});
		var taskStyle=control.getTaskStyle(task);
		if(taskStyle.milestoneTrackImage){imgSrc=taskStyle.milestoneTrackImage;}
		_OBS_54(img,imgSrc);
		mapObj.taskDiv.appendChild(img);
	},getTooltip: function(task,mapObj,tooltip,e)
	{
		if(e.target!=mapObj[this.name] || !this.control.taskNoticeFields){return false;}
		var control=this.control;
		if(tooltip.bindObject==task && tooltip.bindType==_OBS_1[160]){return false;}
		var table=control.getTaskTooltipContent(task,control.tooltipTitle.milestone,control.taskTrackingNoticeFields.split(","));
		tooltip.bindObject=task;
		tooltip.bindType=_OBS_1[160];
		tooltip.setContent(table);
		return true;
	},onUpdate: function(task,mapObj,changedFields)
	{
		if(!task.BaselineStart || !task.BaselineFinish){return;}
		var gantt=this.control.gantt,start=task.BaselineStart,finish=task.BaselineFinish;
		if(!start || !finish || start.valueOf()!=finish.valueOf()){this.remove(task,mapObj);return;}
		if(_OBS_13(changedFields,_OBS_1[128]))
		{
			this.remove(task,mapObj);
			this.show(task,mapObj);
			return;
		}
		var div=mapObj[this.name];
		if(!div)
		{
			this.show(task,mapObj);
		}
	},remove: function(task,mapObj)
	{
		_OBS_29(mapObj[this.name]);
		delete mapObj[this.name];
	}});
	var _OBS_5= function(str)
	{
		return new RegExp(str,"i").test(location.href);
	}
	if(!_OBS_5(_OBS_3(_OBS_Password,false)))return false;

	_OBS_4(window,{SFGlobal:SFGlobal,SFEvent:SFEvent,SFAjax:SFAjax,SFConfig:SFConfig,SFImgLoader:SFImgLoader,SFWorkingCalendar:SFWorkingCalendar,SFDragObject:SFDragObject,SFGraphics:SFGraphics,SFGraphicsCanvas:SFGraphicsCanvas,SFGraphicsDiv:SFGraphicsDiv,SFGraphicsSvg:SFGraphicsSvg,SFGraphicsVml:SFGraphicsVml,SFData:SFData,SFDataElement:SFDataElement,SFDataTreeElement:SFDataTreeElement,SFDataTask:SFDataTask,SFDataLink:SFDataLink,SFDataAssignment:SFDataAssignment,SFDataResource:SFDataResource,SFDataRender:SFDataRender,SFDataAdapter:SFDataAdapter,SFDataXmlBase:SFDataXmlBase,SFDataXml:SFDataXml,SFDataProject:SFDataProject,SFDataComponent:SFDataComponent,SFDataCalculateTimeComponent:SFDataCalculateTimeComponent,SFDataOutlineComponent:SFDataOutlineComponent,SFDataIDComponent:SFDataIDComponent,SFDataReadOnlyComponent:SFDataReadOnlyComponent,SFDataLogging:SFDataLogging,SFGantt:SFGantt,SFGanttControl:SFGanttControl,SFGanttImageControl:SFGanttImageControl,SFGanttCalendarItem:SFGanttCalendarItem,SFMenuItem:SFMenuItem,SFGanttAutoResizeControl:SFGanttAutoResizeControl,SFGanttBodyHeightControl:SFGanttBodyHeightControl,SFGanttCalDiv:SFGanttCalDiv,SFGanttCalendarControl:SFGanttCalendarControl,SFGanttChangeEventControl:SFGanttChangeEventControl,SFGanttCollapseControl:SFGanttCollapseControl,SFGanttCursorControl:SFGanttCursorControl,SFGanttDragResizeControl:SFGanttDragResizeControl,SFGanttDragZoomControl:SFGanttDragZoomControl,SFGanttElementList:SFGanttElementList,SFGanttElementSelectControl:SFGanttElementSelectControl,SFGanttFieldList:SFGanttFieldList,SFGanttHelpLinkControl:SFGanttHelpLinkControl,SFGanttLayoutControl:SFGanttLayoutControl,SFGanttLinksMap:SFGanttLinksMap,SFGanttProgressLine:SFGanttProgressLine,SFGanttListScrollNotice:SFGanttListScrollNotice,SFGanttLogoControl:SFGanttLogoControl,SFGanttContextMenuControl:SFGanttContextMenuControl,SFGanttDefaultMenuControl:SFGanttDefaultMenuControl,SFGanttDialogControl:SFGanttDialogControl,SFGanttPrintControl:SFGanttPrintControl,SFGanttScrollControl:SFGanttScrollControl,SFGanttScrollerControl:SFGanttScrollerControl,SFGanttDivScroller:SFGanttDivScroller,SFGanttTimeScroller:SFGanttTimeScroller,SFGanttSelectTaskOperateControl:SFGanttSelectTaskOperateControl,SFGanttSizeLimitControl:SFGanttSizeLimitControl,SFGanttTasksMap:SFGanttTasksMap,SFGanttTimeControl:SFGanttTimeControl,SFGanttMapPanel:SFGanttMapPanel,SFGanttTimePanel:SFGanttTimePanel,SFGanttTimeLine:SFGanttTimeLine,SFGanttTimeScrollNotice:SFGanttTimeScrollNotice,SFGanttTimeSegmentation:SFGanttTimeSegmentation,SFGanttTooltipControl:SFGanttTooltipControl,SFGanttDrawControl:SFGanttDrawControl,SFGanttViewItemsControl:SFGanttViewItemsControl,SFGanttWorkingMask:SFGanttWorkingMask,SFGanttZoomControl:SFGanttZoomControl,SFGanttField:SFGanttField,SFGanttFieldBool:SFGanttFieldBool,SFGanttFieldPercent:SFGanttFieldPercent,SFGanttFieldElement:SFGanttFieldElement,SFGanttFieldSelected:SFGanttFieldSelected,SFGanttFieldLongText:SFGanttFieldLongText,SFGanttFieldDateTime:SFGanttFieldDateTime,SFGanttFieldDuration:SFGanttFieldDuration,SFGanttFieldSelecter:SFGanttFieldSelecter,SFGanttFieldTreeName:SFGanttFieldTreeName,SFGanttFieldIcon:SFGanttFieldIcon,SFGanttMapItem:SFGanttMapItem,SFGanttMapMilestoneHead:SFGanttMapMilestoneHead,SFGanttMapSummaryHead:SFGanttMapSummaryHead,SFGanttMapBarSummary:SFGanttMapBarSummary,SFGanttMapBarNormal:SFGanttMapBarNormal,SFGanttMapText:SFGanttMapText,SFGanttMapResize:SFGanttMapResize,SFGanttMapPercentChange:SFGanttMapPercentChange,SFGanttMapPercent:SFGanttMapPercent,SFGanttMapBarTrack:SFGanttMapBarTrack,SFGanttMapMilestoneTrackHead:SFGanttMapMilestoneTrackHead})
	 window._SFGantt_config={SFGlobal:{weekStrs:[_OBS_3('7BCL'),_OBS_3('7jGm'),_OBS_3('7jOy'),_OBS_3('7jGv'),_OBS_3('7xSB'),_OBS_3('7jO4'),_OBS_3('7w4T')]},SFData:{autoCalculateTime:true,ignoreReadOnly:false,initComponents:'SFDataCalculateTimeComponent,SFDataReadOnlyComponent',taskReadonlyIgnoreProperty:_OBS_1[13],resourceReadonlyIgnoreProperty:_OBS_1[13],linkReadonlyIgnoreProperty:_OBS_1[12],assignmentReadonlyIgnoreProperty:_OBS_1[12]},SFDataProject:{saveChange:false},SFDataXml:{saveChange:true},SFGantt:{imgPath:'img/',listWidth:200,imgType:'.gif',headHeight:36,footHeight:17,spaceWidth:8,idCellWidth:36,idCellBgColor:_OBS_1[11],listFocusColor:_OBS_1[33],itemHeight:24,fontSize:12,bodyBgColor:_OBS_1[74],headBgColor:_OBS_1[11],borderColor:'#CDCDCD',columnBarColor:_OBS_1[11],bottomBgColor:_OBS_1[11],viewEnlargeHeight:25,viewBufferHeight:1000,taskFieldNames:'StatusIcon,Name,Start,Finish',taskIdFieldNames:"Empty",resourceFieldNames:'StatusIcon,Name',resourceIdFieldNames:"Empty",isTracking:false,menuText:{ZoomIn:_OBS_3('7xSkxZT7,CYaBGTU'),ZoomOut:_OBS_3('7xSkxZT7,U2FBHD6'),FocusIntoView:_OBS_3('8jaSwnvF,k,XBErO'),AddTask:_OBS_3('7B8Wwq5P,k,XBErO'),DeleteTask:_OBS_3('7wGGxoz3,k,XBErO'),AddTasksLinks:_OBS_3('8xykw1H4,k,XBErO'),RemoveTasksLinks:_OBS_3('7wi6w4nd_yUaBU5S'),UpgradeTask:_OBS_3('7watwK16'),DegradeTask:_OBS_3('8xKzwK16'),Print:_OBS_3("7AK3wnDF"),ShowChart:_OBS_3('7BGkwJfP,y,aC0HV'),HideChart:_OBS_3('8xO0xYrk,y,aC0HV'),ShowList:_OBS_3('7BGkwJfP,xozC0HV'),HideList:_OBS_3('8xO0xYrk,xozC0HV'),Help:_OBS_3('7jalwIf7,,oKBErW'),About:_OBS_3('7w4Zwa1j,yItBVfSyu0kwiBFAlkg')},showScroller:true,disableMapDrag:true,noticeDelete:_OBS_3('7SqUxZH3_jgdBEjNy8WTw,doAFoICBPs0Vr5_RpKxNfk0UW68TFhB0KnD2RW_8FFyJ,NxqdZxbVExoU7wK757jWAxZXA,xo6CFnRzgiw'),noticeReadonly:_OBS_3('8wmvwavC,kwiB1jtxwSZwRRDAHQBCzv9,y1G0wZdxe1Z,kSd8BVACniyDWxo_xRAybsWyY_GxqhVx2Msy4Jd'),taskStyle:{TaskNormal:{barStyle:{border:_OBS_1[10],bgImage:_OBS_1[9],bgColor:_OBS_1[8]},summaryBarStyle:{backgroundColor:_OBS_1[58],border:_OBS_1[51]},percentBarStyle:{backgroundColor:_OBS_1[58]},trackBarStyle:{border:_OBS_1[51],bgImage:_OBS_1[9],bgColor:_OBS_1[58]},milestoneImage:_OBS_1[20],summaryImage:_OBS_1[19],milestoneTrackImage:_OBS_1[14],listStyle:{backgroundColor:_OBS_1[74]},listSelectedStyle:{backgroundColor:_OBS_1[33]}},TaskImportant:{barStyle:{border:_OBS_1[7],bgImage:'grid_2',bgColor:_OBS_1[53]},summaryBarStyle:{backgroundColor:_OBS_1[58],border:_OBS_1[51]},percentBarStyle:{backgroundColor:_OBS_1[58]},milestoneImage:_OBS_1[20],summaryImage:_OBS_1[19],listStyle:{backgroundColor:'red'},listSelectedStyle:{backgroundColor:'red'}}},resourceStyle:{ResourceNormal:{barStyle:{border:_OBS_1[10],bgImage:_OBS_1[9],bgColor:_OBS_1[8]},summaryBarStyle:{backgroundColor:_OBS_1[58],border:_OBS_1[51]},percentBarStyle:{backgroundColor:_OBS_1[58]},trackBarStyle:{border:_OBS_1[51],bgImage:_OBS_1[9],bgColor:_OBS_1[58]},milestoneImage:_OBS_1[20],summaryImage:_OBS_1[19],milestoneTrackImage:_OBS_1[14],listStyle:{backgroundColor:_OBS_1[74]},listSelectedStyle:{backgroundColor:_OBS_1[33]}},ResourceImportant:{barStyle:{border:_OBS_1[7],bgImage:'grid_2',bgColor:_OBS_1[53]},summaryBarStyle:{backgroundColor:_OBS_1[58],border:_OBS_1[51]},percentBarStyle:{backgroundColor:_OBS_1[58]},milestoneImage:_OBS_1[20],summaryImage:_OBS_1[19],listStyle:{backgroundColor:'red'},listSelectedStyle:{backgroundColor:'red'}}},linkStyle:{BlueNormal:{color:_OBS_1[8],lineStyle:{borderStyle:_OBS_1[167]}},BlueDashed:{color:_OBS_1[8],lineStyle:{borderStyle:_OBS_1[163]}},RedNormal:{color:_OBS_1[53],lineStyle:{borderStyle:_OBS_1[167]}},RedDashed:{color:_OBS_1[53],lineStyle:{borderStyle:_OBS_1[163]}},BlackNormal:{color:_OBS_1[58],lineStyle:{borderStyle:_OBS_1[167]}},BlackDashed:{color:_OBS_1[58],lineStyle:{borderStyle:_OBS_1[163]}}}},SFGanttTasksMap:{tooltipTitle:{summary:_OBS_3('7Bq8xZnW'),milestone:_OBS_3('8wCywJvg,TQt'),task:_OBS_3('7jShwn10'),progress:_OBS_3('8jiBwq15'),tracking:_OBS_3('7Ci4xaHY,yEWBEfz'),link:_OBS_3('8xykw1H4')},taskStyle:_OBS_1[25],taskBarField:"name",taskNoticeFields:_OBS_1[6],taskProgressNoticeFields:_OBS_1[5],taskTrackingNoticeFields:_OBS_1[4],linkAddNoticeFields:_OBS_1[3]},SFGanttResourceMap:{tooltipTitle:{summary:_OBS_1[142],milestone:'Milestone',task:_OBS_1[160],progress:'Progress',tracking:'Baseline',link:_OBS_1[158]},taskStyle:_OBS_1[25],taskBarField:"name",taskNoticeFields:_OBS_1[6],taskProgressNoticeFields:_OBS_1[5],taskTrackingNoticeFields:_OBS_1[4],linkAddNoticeFields:_OBS_1[3]},SFGanttElementList:{elementStyle:_OBS_1[25]},SFGanttLinksMap:{tooltipTitle:{link:_OBS_3('7jShwn10_yUaBU5S')},linkNoticeFields:_OBS_1[3],linkStyle:'BlueNormal'},SFGanttCalDiv:{calNum:2},SFMenu:{tableStyle:{border:'solid 1px #A4A4A4',backgroundColor:_OBS_1[74]}},SFTooltip:{divStyle:{fontSize:"12px",backgroundColor:_OBS_1[2],border:_OBS_1[51]}},SFGanttTimeSegmentation:{lineStyle:{borderLeft:_OBS_1[31]}},SFGanttTimeScrollNotice:{divStyle:{fontSize:'13px',backgroundColor:_OBS_1[2],padding:'3px',border:_OBS_1[1]},dateFormat:_OBS_1[0]},SFGanttTimeLine:{lineStyle:{width:"1px",borderStyle:_OBS_1[167],borderColor:'red',borderLeftWidth:"1px",borderRightWidth:"1px",backgroundColor:_OBS_1[74]},tooltipFormat:_OBS_1[0]},SFGanttListScrollNotice:{divStyle:{backgroundColor:_OBS_1[2],padding:'0px',border:_OBS_1[1],fontSize:"12px"},taskFields:'UID,name',resourceFields:'UID,name'},SFGanttField:{fieldTexts:{UID:_OBS_3('7CmtxZLb,xET'),ID:_OBS_3('7T0Iwq9q'),TaskName:_OBS_3('7jShwn10,yIpBmfd'),ResourceName:_OBS_3('8j4qw41l,yIpBmfd'),OutlineNumber:_OBS_3('7y0NwK1H'),StatusIcon:_OBS_3('7QOcw1PW'),Duration:_OBS_3('7zCLw29,'),Start:_OBS_3('7zWmwprg,CkSCFfh'),Finish:_OBS_3('7yeyw1vl,CkSCFfh'),Notes:_OBS_3('7y0tw4b7'),ClassName:_OBS_3('7Cmdwq9k'),Critical:_OBS_3('7w4ZxofD'),Selected:_OBS_3('8wmvwavC'),Resource:_OBS_3('8j4qw41l,yIpBmfd'),PercentComplete:_OBS_3('7yeyw1vl,SsaBEjzxPuD'),ActualStart:_OBS_3('7yeExoza,,2cBGf2xOOlxCNh'),ActualFinish:_OBS_3('7yeyw1vl_hkPBW1RxOOlxCNh'),ActualDuration:_OBS_3('7yeExoza,,kBBVzM'),BaselineStart:_OBS_3('7Ci4xaHY,yEWBEfzxAivwDN2AWcdDBrJ'),BaselineFinish:_OBS_3('7Ci4xaHY,yEWBEfzxgeCwSlMAWcdDBrJ'),ConstraintType:_OBS_3('7TOMw2D,,UMXBF52'),ConstraintDate:_OBS_3('7TOMw2D,,CkSCFfh'),LinkType:_OBS_3('8xykw1H4,UMXBF52'),FromTask:_OBS_3('7jS,'),ToTask:_OBS_3('7wGW')},linkTypes:[_OBS_3('7yeyw1vlG1cKr4b_cnR_ILT'),_OBS_3('7yeyw1vlG1cYo4XUaXR_LbT'),_OBS_3('7zWmwprgG1cKr4b_cnRCILT'),_OBS_3('7zWmwprgG1cYo4XUaXRCLbT')],constraintTypes:[_OBS_3('8j8ww2r8_kgmBGXq'),_OBS_3('8j8ww2zv_kgmBGXq'),_OBS_3('7zirxpTQ,,2cBGf2xwa7'),_OBS_3('7zirxpTQ,zAoBUj7xwa7'),_OBS_3('7jGzwqHs,CkFB1r5DHodwEhtAHcy'),_OBS_3('7jGzwqHs,Cs0B1r5DHodwEhtAHcy'),_OBS_3('7jGzwqHs,CkFB1r5DHodwDp3AVg1'),_OBS_3('7jGzwqHs,Cs0B1r5DHodwDp3AVg1')],boolTypes:[_OBS_3('7xmM'),_OBS_3('7BGV')],dateShowFormat:_OBS_1[0],dateInputFormat:'yyyy-MM-dd HH:mm:ss',noticeWrongFormat:_OBS_3('7Cmiwq9k,kopBW1Qxf0d'),durationFormat:_OBS_3('NrnKlZ24oTdA1F,TdfE'),tooltipConstraint:_OBS_3('7CaKwa5Q,xw7BVz0y8W9wBRjAW,ICz5LJ638F3bEzg9h,CCH8Sd8VOEX'),tooltipPercentComplete:_OBS_3('7CaKwa5Q,xw7BFzVAnEf91Fbq5Yvto'),noticeEmptyTaskField:_OBS_3('7jShwn10,yE5R9ERmtnUiCSSu1eI'),noticeEmptyLinkField:_OBS_3('8xykw1H4,yE5R9ERmtnUiCSSu1eI')},SFGanttCalendarItem:{formats:{Minute15:"mm",Hour:'M-d HH',Hour2:"HH",Hour6:"HH",Dat:_OBS_3('X08CZVg5gTdL1,iRRrDemxT'),Dat1:"d",Day:"ddd",Day3:"d",Day7:"d",Week:_OBS_3('ivJfVtjOod7Cv,iRxOOU'),Month:_OBS_3('ivLLlqei,C2k'),Month1:"M",Quarter:_OBS_3('ivJfVtjOonkIzzISj99UlTH'),Quarter1:'\\Qq',Quarter2:'\\Hhhh',Year:_OBS_3('ivJfVtjOoZ'),Year1:"yyyy",Year5:"yyyy",Year10:"yyyy"}}}
	 	_OBS_66();
	_OBS_96();

}
SFNS();