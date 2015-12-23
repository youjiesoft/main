function PL_rili(_id,Option,json_val){
	var eval = JSON.parse(json_val); 
    //如果页面中不包含该对象则退出该扩展方法
    if(!_id){return false;}
    var Today=new Date();
    var tY=Today.getFullYear();
    var tM=Today.getMonth();
    var tD=Today.getDate();
    //農曆資料
    var lunarInfo=new Array(0x04bd8,0x04ae0,0x0a570,0x054d5,0x0d260,0x0d950,0x16554,0x056a0,0x09ad0,0x055d2,0x04ae0,0x0a5b6,0x0a4d0,0x0d250,0x1d255,0x0b540,0x0d6a0,0x0ada2,0x095b0,0x14977,0x04970,0x0a4b0,0x0b4b5,0x06a50,0x06d40,0x1ab54,0x02b60,0x09570,0x052f2,0x04970,0x06566,0x0d4a0,0x0ea50,0x06e95,0x05ad0,0x02b60,0x186e3,0x092e0,0x1c8d7,0x0c950,0x0d4a0,0x1d8a6,0x0b550,0x056a0,0x1a5b4,0x025d0,0x092d0,0x0d2b2,0x0a950,0x0b557,0x06ca0,0x0b550,0x15355,0x04da0,0x0a5d0,0x14573,0x052d0,0x0a9a8,0x0e950,0x06aa0,0x0aea6,0x0ab50,0x04b60,0x0aae4,0x0a570,0x05260,0x0f263,0x0d950,0x05b57,0x056a0,0x096d0,0x04dd5,0x04ad0,0x0a4d0,0x0d4d4,0x0d250,0x0d558,0x0b540,0x0b5a0,0x195a6,0x095b0,0x049b0,0x0a974,0x0a4b0,0x0b27a,0x06a50,0x06d40,0x0af46,0x0ab60,0x09570,0x04af5,0x04970,0x064b0,0x074a3,0x0ea50,0x06b58,0x055c0,0x0ab60,0x096d5,0x092e0,0x0c960,0x0d954,0x0d4a0,0x0da50,0x07552,0x056a0,0x0abb7,0x025d0,0x092d0,0x0cab5,0x0a950,0x0b4a0,0x0baa4,0x0ad50,0x055d9,0x04ba0,0x0a5b0,0x15176,0x052b0,0x0a930,0x07954,0x06aa0,0x0ad50,0x05b52,0x04b60,0x0a6e6,0x0a4e0,0x0d260,0x0ea65,0x0d530,0x05aa0,0x076a3,0x096d0,0x04bd7,0x04ad0,0x0a4d0,0x1d0b6,0x0d250,0x0d520,0x0dd45,0x0b5a0,0x056d0,0x055b2,0x049b0,0x0a577,0x0a4b0,0x0aa50,0x1b255,0x06d20,0x0ada0);
    var solarMonth=new Array(31,28,31,30,31,30,31,31,30,31,30,31);
    var Gan=new Array("甲","乙","丙","丁","戊","己","庚","辛","壬","癸");
    var Zhi=new Array("子","丑","寅","卯","辰","巳","午","未","申","酉","戌","亥");
    var solarTerm=new Array("小寒", "大寒", "立春", "雨水", "惊蛰", "春分", "清明", "谷雨", "立夏", "小满", "芒种", "夏至", "小暑", "大暑", "立秋", "处暑", "白露", "秋分", "寒露", "霜降", "立冬", "小雪", "大雪", "冬至")
    var sTermInfo=new Array(0,21208,42467,63836,85337,107014,128867,150921,173149,195551,218072,240693,263343,285989,308563,331033,353350,375494,397447,419210,440795,462224,483532,504758);
    //var nStr1=new Array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');
    var nStr1=new Array('周日','周一','周二','周三','周四','周五','周六');
    var cn_mth=new Array("","一月","二月","三月","四月","五月","六月","七月","八月","九月","十月","十一月","十二月");
    var cn_day=new Array("","初一","初二","初三","初四","初五","初六","初七","初八","初九","初十","十一","十二","十三","十四","十五","十六","十七","十八","十九","二十","廿一","廿二","廿三","廿四","廿五","廿六","廿七","廿八","廿九","三十","卅一");
    var wFtv=new Array("0520 母情节","1144 感恩节");//某月的第几个星期几
    //國曆節日 *表示放假日
    var sFtv=new Array(
            "0101 元旦",
            "0106  中国13亿人口日",
            "0110  中国110宣传日",

            "0202  世界湿地日",
            "0204  世界抗癌症日",
            "0210  世界气象日",
            "0214  情人节",
            "0221  国际母语日",
            "0207  国际声援南非日",

            "0303  全国爱耳日",
            "0308  妇女节",
            "0312  植树节 孙中山逝世纪念日",
            "0315  消费者权益保护日",
            "0321  世界森林日",
            "0322  世界水日",
            "0323  世界气象日",
            "0324  世界防治结核病日",

            "0401  愚人节",
            "0407  世界卫生日",
            "0422  世界地球日",

            "0501 国际劳动节",
            "0504  中国青年节",
            "0505  全国碘缺乏病日",
            "0508  世界红十字日",
            "0512  国际护士节",
            "0515  国际家庭日",
            "0517  世界电信日",
            "0518  国际博物馆日",
            "0519  中国汶川地震哀悼日 全国助残日",
            "0520  全国学生营养日",
            "0522  国际生物多样性日",
            "0523  国际牛奶日",
            "0531  世界无烟日",

            "0601  国际儿童节",
            "0605  世界环境日",
            "0606  全国爱眼日",
            "0617  防治荒漠化和干旱日",
            "0623  国际奥林匹克日",
            "0625  全国土地日",
            "0626  国际反毒品日",

            "0701  建党节 香港回归纪念日",
            "0707  抗日战争纪念日",
            "0711  世界人口日",

            "0801  八一建军节",
            "0815  日本正式宣布无条件投降日",

            "0908  国际扫盲日",
            "0909  毛泽东逝世纪念日",
            "0910  教师节",
            "0916  国际臭氧层保护日",
            "0917  国际和平日",
            "0918  九·一八事变纪念日",
            "0920  国际爱牙日",
            "0927  世界旅游日",
            "0928  孔子诞辰",

            "1001 国庆节 国际音乐节 国际老人节",
            "1002  国际减轻自然灾害日",
            "1004  世界动物日",
            "1007  国际住房日",
            "1008  世界视觉日 全国高血压日",
            "1009  世界邮政日",
            "1010  辛亥革命纪念日 世界精神卫生日",
            "1015  国际盲人节",
            "1016  世界粮食节",
            "1017  世界消除贫困日",
            "1022  世界传统医药日",
            "1024  联合国日",
            "1025  人类天花绝迹日",
            "1026  足球诞生日",
            "1031  万圣节",

            "1107  十月社会主义革命纪念日",
            "1108  中国记者日",
            "1109  消防宣传日",
            "1110  世界青年节",
            "1112  孙中山诞辰",
            "1114  世界糖尿病日",
            "1117  国际大学生节",

            "1201  世界艾滋病日",
            "1203  世界残疾人日",
            "1209  世界足球日",
            "1210  世界人权日",
            "1212  西安事变纪念日",
            "1213  南京大屠杀",
            "1220  澳门回归纪念日",
            "1221  国际篮球日",
            "1224  平安夜",
            "1225  圣诞节 世界强化免疫日",
            "1226  毛泽东诞辰");
  //农历节日  *表示放假日
    var lFtv = new Array(
            "0101 春节",
            "0102 大年初二",
            "0103 大年初三",
            "0105  路神生日",
            "0115  元宵节",
            "0202  龙抬头",
            "0219  观世音圣诞",
            "0404  寒食节",
            "0408  佛诞节 ",
            "0505 端午节",
            "0606  天贶节 姑姑节",
            "0624  彝族火把节",
            "0707  七夕情人节",
            "0714  鬼节(南方)",
            "0715  盂兰节",
            "0730  地藏节",
            "0815 中秋节",
            "0909  重阳节",
            "1001  祭祖节",
            "1117  阿弥陀佛圣诞",
            "1208  腊八节 释迦如来成道日",
            "1223  过小年",
            "0100 除夕");
    //年月日
    Show(tY,tM,tD);
    function Show(_y,_m,_d){
        var i,sD;
        var cld=new core(_y,_m);
        Ganzhi((_y-1900)*12 + _m +14);
        css='<style type="text/css">';
        css+='#PL_rili_tit{background-color:#2aaedf;padding:6px;font-family:Verdana,arial;font-size:13px;font-weight:bold;color:#444;position:relative;}';
        css+='#PL_rili_tit div{margin-bottom:4px;color:#fff;}';
        css+='#PL_rili_tit .btn{width:auto;background:none;border:none;color:#FFF;font-size:12px;font-weight:bold;height:23px;padding:3px;cursor:pointer;}';
        css+='#PL_rili_box{width:100%;font-family:Verdana,arial,Pmingliu;font-size:12px;color:#444;position:relative;}';
        css+='#PL_rili_box td{text-align:center;}#PL_rili_box input{border-width:1px;width:98%;}';
        css+='#PL_rili_box #r1 td{padding:4px 0;text-align:center;vertical-align:top;}';
        css+='#PL_rili_box #r2{background:#fff;height:39px;vertical-align:top;color:#444;}';
        css+='#PL_rili_box #r2 td{}';
        css+='#PL_rili_box #r2 div{overflow:hidden;position:relative;width:100%;padding:10px 0 0;}';
        css+='#PL_rili_box #r2 .a{display:none;float:right;padding-right:3px;font-size:12px;font-weight:normal;}';
        css+='#PL_rili_box #r2 .b{}#PL_rili_box #r2 .b b{font-size:12px;}#PL_rili_box #r2 .b em{color:#888;font-style:normal;font-size:12px;}';
        css+='#PL_rili_box #r2 .c{color:#333;float:left;text-align:left;text-indent:4px;width:100%;font-size:0;font-weight:normal;}';
        css+='</style>';
        css+='<div id="PL_rili_tit">';
        css+='<div><button class="btn btnLeft btnCtrl" id="del">«</button><button class="btn btnMother" id="today">本月</button><button class="btn btnRight btnCtrl" id="add">»</button></div>';//◄►
        css+='<div class="timeData">'+_y+'年'+(_m+1)+'月'+_d+'日 ';
        //if(_y>1911){css+='民國'+(((_y-1911)==1)?'元':_y-1911)+'年'+(_m+1)+'月 ';}
        //css+=Ganzhi(_y-1900+36)+'年 '+Ganzhi((_y-1900)*12+_m+14)+'月';
        css+='</div><input type="hidden" id="y_" value="'+_y+'"><input type="hidden" id="m_" value="'+(_m+1)+'"></div>';
        css+='<table id="PL_rili_box" border="0" cellspacing="0" cellpadding="0">';//星期
        css+='<tr id="r1">';
        for(i=0;i<7;i++){css+='<td style="width:14.2857%;">'+nStr1[i]+'</td>';}
        css+='</tr>';
        //42天
        css+='<tr id="r2">';
        for(i=0;i<42;i++){
            sD=i-cld.firstWeek;
            if(sD>=0 && sD < cld.length){
                css+='<td class="'+cld[sD].bStyle+'">';
                //显示今天的颜色
                //css+=(cld[sD].bColor)?' style="border: 2px solid;border-color:'+cld[sD].bColor+';">':'>';
                css+='<div';
                if(cld[sD].bColor || cld[sD].cColor){
                    //显示节假日或者今天的颜色
                    //css+=(cld[sD].cColor)?' style="border: 1px solid;border-color:'+cld[sD].cColor+';"':' style="border: 1px solid;border-color:'+cld[sD].bColor+';"';
                }
                css+='>';
                //农历
                css+='<span class="a">';
                css+=(Option[0])?cld[sD].JieRi+' ':'';            //节日
                css+=(Option[1])?' '+cld[sD].Ganzhi:'';            //干支
                css+='</span>';
                //公历法定节假日
                css+='<span class="b"';
                css+=(Option[3])?' style="color:'+cld[sD].aColor+'"':'';
                css+='>';
                var abc = new Date(_y,_m,sD+1);
                var nowtime = abc.getTime();
                var strtotimeval = nowtime/1000;
                var $num = 0;
                var $cnum = 0;
                if(eval!=""){
                	for(var ev in eval){
                		if(ev == strtotimeval){
                			for(var self in eval[ev]){
                				if(self == 1){
                					css+='<a href="javascript:;" title="单击查看日程" rel='+strtotimeval+' class="plugin_click"><span class="calendar-triangle"></span>';
                				}else if(self == 2){
                					css+='<a href="javascript:;" title="单击查看日程" rel='+strtotimeval+' class="plugin_click"><span class="calendar-triangle-a"></span>';
                				}else{
                					css+='<a href="javascript:;" title="单击查看日程" rel='+strtotimeval+' class="plugin_click"><span class="calendar-triangle-b"></span>';
                				}
                			}
                			$cnum++;
                		}else{
                			if($num === 0 && $cnum === 0 ){
                				css+='<a href="javascript:;" title="双击新增日程" rel='+strtotimeval+' class="plugin_dblclick">';
                				$num++;
                			}
                		}
                	}
                }else{
                	css+='<a href="javascript:;" title="双击新增日程" rel='+strtotimeval+' class="plugin_dblclick">';
                }
                css+='<b>'+(sD+1)+'日</b><br/><em>';   //css+='<b>'+(sD+1)+'日</b><br/><em>';
                if(Option[2]){                                    //农历
                    if(cld[sD].lDay==1){
                        if(cld[sD].RnYue){css+='润';}
                        css+=cn_mth[cld[sD].lMonth];            //农历月份
                    }else{
                        if(cld[sD].JieQi){
                            css+=cld[sD].JieQi;                    //显示农历节气
                        }else{
                            css+=cn_day[cld[sD].lDay];            //显示农历日期
                        }
                    }
                }
                css+='</em></a></span></div>';
                //房价
                css+='<span id="'+_y+'-'+(_m+1)+'-'+(sD+1)+'" class="c"></span>';
                css+='</td>';
            }else{
                css+='<td>&nbsp;</td>';
            }
            if((i%7==6) && i<41){
                if((sD+2)>cld.length){break;}//css+='</tr><tr id="r2">';
            }
            if((i+1)%7==0 && i<41){css+='</tr><tr id="r2" name="'+i+'">';}
        }
        css+='</tr></table>';
        $(_id).append(css);
        //調整佈局
        $("#PL_rili_box #r1 td:last-child").css("border-right","none");
        $("#PL_rili_box #r2 td:last-child").css("border-right","none");
        //手工修改月份
        $("#PL_rili_tit #del").click(function(){Press("-");});
        $("#PL_rili_tit #add").click(function(){Press("+");});
        $("#PL_rili_tit #today").click(function(){Press("=");});
        $("#PL_rili_box #r2 a.plugin_click").click(function (){plugin_click(this);});
        $("#PL_rili_box #r2 a.plugin_dblclick").dblclick(function (){plugin_dblclick(this);});
        $("#PL_rili_box #r2 a.plugin_click").mouseover(function (){plugin_mouseover(this);});
        //modify by quqiang 20140530 插件调用
        //$("#PL_rili_box #r2 a.plugin_click").mouseout(function (){plugin_mouseout(this);});
        //add by quqiang 20140530 插件调用
        $('#PL_rili_box #r2 a.plugin_click').nbmtip({order:'#calendar-tips'});
    }
    function plugin_dblclick(obj){
    	//获取当前时间戳
    	var startTime=$(obj).attr("rel");
    	var endTime = parseInt(startTime)+parseInt(86399);
    	var options={};
        options.width=700;
        options.height=565;
        options.mask=true;
        $.pdialog.open(TP_APP+"/MisUserEvents/add/stepType/1/enddate/"+endTime+"/startdate/"+startTime,'add','新增日程 ',options);
    }
    function plugin_mouseover(obj){
    	//获取当前时间戳
    	var reltime=$(obj).attr("rel");
    	if(eval){
         	for(var ev in eval){
         		if(ev == reltime){
         			for(var sf in eval[ev]){
         				var d="";
             			for(var $i=0;$i<eval[ev][sf].length; $i++){
             				if($i<2){
             					d+='<li class="calendar-tips-alt"><div class="calendar-tips-item">';
                 				d+='<div class="tml-legend tml-text-c tml-mb5">'+eval[ev][sf][$i]['text']+'</div>';
                 				if(eval[ev][sf][$i]['personname']){
                 					d+='<div class="tml-mb5 calendar-tips-poser"><span class="tml-labe tml-mr3">相关人员:</span>'+eval[ev][sf][$i]['personname']+'</div>';
                 				}
                 				d+='<div class="calendar-tips-content"><span class="tml-labe tml-mr3">日程内容:</span>'+eval[ev][sf][$i]['details']+'</div></div></li>';
             				}
             			}
             			if(eval[ev][sf].length>2){
             				d+='<div class="tml-text-r">更多</div>';
             			}
         			}
         		}
         	}
         	$("#calendar-tips-list").text("");
         	$("#calendar-tips-list").append(d);
         }
    	//modify by quqiang 20140530 插件调用
    	//$("#calendar-tips").show();
    }
    
    
    function plugin_mouseout(obj){
    	$("#calendar-tips").hide();
    }
    function plugin_click(obj){
    	var url=TP_APP+'/MisUserEvents/index';
    	navTab.openTab('MisUserEvents', url, {
			title : '重要日程',
			fresh : true,
			data : {}
		});
    }
    
    //手工修改月份
    function Press(_t){
        try{
            hiddenY=parseInt($("#PL_rili_tit #y_").val());
            hiddenM=parseInt($("#PL_rili_tit #m_").val());
        }catch(e){
            hiddenY=tY;hiddenM=tM;
        }
        hiddenM--;
        switch(_t.toLowerCase()){
        case "-":
            if(hiddenM>0){hiddenM--;}else{hiddenM=11;if(hiddenY>0){hiddenY--;}}
            break;
        case "+":
            if(hiddenM<11){hiddenM++;}else{hiddenM=0;hiddenY++;}
            break;
        case "=":
            hiddenY=tY;hiddenM=tM;
            break;
        }
        $(_id).empty();
        Show(hiddenY,hiddenM,tD);
    }
    //傳回月曆物件 (y年,m+1月)
    function core(y,m){
    	 var sDObj,lDObj,lY,lM,lD=1,lL,lX=0,tmp1,tmp2;
    	 var cY, cM, cD; //年柱,月柱,日柱
	    var lDPOS = new Array(3);
	    var n = 0;
	    var firstLM = 0;
	    sDObj = new Date(y, m, 1, 0, 0, 0, 0);    //当月一日日期
	    this.length = solarDays(y, m);    //公历当月天数			//31
	    this.firstWeek = sDObj.getDay();    //公历当月1日星期几   	//天
        for(var i=0;i<this.length;i++){
        	if (lD > lX) {
                sDObj = new Date(y, m, i + 1);    //当月一日日期
                lDObj = new Lunar(sDObj);     //农历
                lY = lDObj.year;           //农历年
                lM = lDObj.month;          //农历月
                lD = lDObj.day;            //农历日
                lL = lDObj.isLeap;         //农历是否闰月
                lX = lL ? leapDays(lY) : monthDays(lY, lM); //农历当月最后一天

                if (n == 0) firstLM = lM;
                lDPOS[n++] = i - lD + 1;
            }
            this[i]=new calElement(lY,lM,lD++,lL,'#444','','','','&nbsp;','');    //年,月,日,閏月,公曆顏色,背景色,節氣
            this[i].Ganzhi=Ganzhi(lDObj.dayCyl++);                            //天干地支
            if((i+this.firstWeek)%7==0){this[i].aColor='red';}                //周日颜色
            if((i+this.firstWeek)%7==6){this[i].aColor='red';}                //周六颜色
            //封装class 为了区别过去现在和未来
            if(y<tY){
            	this[i].bStyle = 'timeBefore';
            }else if(y==tY){
            	if(m<tM){
            		this[i].bStyle = 'timeBefore';
            	}else if(m==tM){
            		if(i<tD-1){
            			this[i].bStyle = 'timeBefore';
            		}else if(i==tD-1){
            			this[i].bStyle = 'timeToday';
            		}else{
            			this[i].bStyle = 'timeAfter';
            		}
            	}else{
            		this[i].bStyle = 'timeAfter';
            	}
            }else{
            	this[i].bStyle = 'timeAfter';
            }
            
        }
        //节气
        tmp1 = sTerm(y, m * 2) - 1;
        tmp2 = sTerm(y, m * 2 + 1) - 1;
        this[tmp1].JieQi = solarTerm[m * 2];
        this[tmp2].JieQi = solarTerm[m * 2 + 1];
        //if (m == 3) this[tmp1].color = 'red'; //清明颜色
        
        //节假日
        for(i in sFtv){
            if(sFtv[i].match(/^(\d{2})(\d{2})([\s\*])(.+)$/)){
                if(Number(RegExp.$1)==(m+1)){
                    if(RegExp.$3=='*'){
                    	this[Number(RegExp.$2)-1].JieRi+=RegExp.$4;
                    	this[Number(RegExp.$2)-1].cColor='#ED9495';
                    }
                }
            }
        }
        for(i in wFtv){
            if(wFtv[i].match(/^(\d{2})(\d)(\d)([\s\*])(.+)$/)){
                if(Number(RegExp.$1)==(m+1)){
                    tmp1=Number(RegExp.$2);tmp2=Number(RegExp.$3);
                    this[((this.firstWeek>tmp2)?7:0) + 7*(tmp1-1) + tmp2 - this.firstWeek].JieRi+=RegExp.$5;
                }
            }
        }
      //农历节日
        for (i  in  lFtv)
            if (lFtv[i].match(/^(\d{2})(.{2})([\s\*])(.+)$/)) {
                tmp1 = Number(RegExp.$1) - firstLM
                if (tmp1 == -11)  tmp1 = 1
                if (tmp1 >= 0 && tmp1 < n) {
                    tmp2 = lDPOS[tmp1] + Number(RegExp.$2) - 1
                    if (tmp2 >= 0 && tmp2 < this.length) {
                        if (RegExp.$3 == '*'){
                        	this[tmp2].JieRi += RegExp.$4 + '  '
                        	this[tmp2].cColor = '#ED9495'
                        }
                    }
                }
            }
        //黑色星期五
        if ((this.firstWeek + 12) % 7 == 5)
            this[12].JieRi += '黑色星期五';
        
        //今日
        if(y==tY && m==tM){this[tD-1].bColor='#e67822';}
    }
    //傳回農曆 y年的總天數
    function lYearDays(y){
        var i,sum=348;
        for(i=0x8000;i>0x8;i>>=1){sum+=(lunarInfo[y-1900] & i)?1:0;}
        return(sum+leapDays(y));
    }
    //傳回農曆 y年閏月的天數
    function leapDays(y){
        if(leapMonth(y)){return((lunarInfo[y-1900] & 0x10000)?30:29);}
        else{return(0);}
    }
    //傳回農曆 y年閏哪個月 1-12 ,沒閏傳回 0
    function leapMonth(y){
        return(lunarInfo[y-1900] & 0xf);
    }
    //傳回農曆 y年m月的總天數
    function monthDays(y,m){
        return((lunarInfo[y-1900] & (0x10000>>m))? 30: 29);
    }
    //得知某天星期幾 getWeek("2011/4/14");
    function getWeek(_day){
        var day = new Date(Date.parse(_day));
        return(day.getDay());
    } 
    //算出農曆,傳入日期物件,傳回農曆日期物件
    //該物件屬性有 .year .month .day .RnYue .dayCyl .monCyl
    function Lunar(objDate){
        var i,leap=0,temp=0;
        var baseDate=new Date(1900,0,31);
        var offset=(objDate - baseDate)/86400000;
        this.dayCyl=offset + 40;
        this.monCyl=14;
            for(i=1900;i<2050 && offset>0;i++){
            temp=lYearDays(i);
            offset-=temp;
            this.monCyl+=12;
        }
        if(offset<0){offset+=temp;i--;this.monCyl-=12;}
        this.year=i;
        leap=leapMonth(i); //閏哪個月
        this.RnYue=false;
        for(i=1;i<13 && offset>0;i++){
            if(leap>0 && i==(leap+1) && this.RnYue==false){//閏月
                --i;this.RnYue=true;temp=leapDays(this.year);
            }else{
                temp=monthDays(this.year,i);
            }
            //解除閏月
            if(this.RnYue==true && i==(leap+1)){this.RnYue=false;}
            offset-=temp;
            if(this.RnYue==false){this.monCyl++;}
        }
        if(offset==0 && leap>0 && i==leap+1){
            if(this.RnYue){
                this.RnYue=false;
            }else{
                this.RnYue=true;--i;--this.monCyl;
            }
        }
        if(offset<0){offset+=temp;--i;--this.monCyl;}
        this.month=i;
        this.day=offset+1;
    }
    //傳回國曆 y年某m+1月的天數
    function solarDays(y,m){
        if(m==1){
            return(((y%4==0) && (y%100 !=0) || (y%400==0))?29:28);
        }else{
            return(solarMonth[m]);
        }
    }
    // 傳入 offset 傳回干支,0=甲子
    function Ganzhi(num){
        return(Gan[num%10]+Zhi[num%12]);
    }
    //============================== 阴历属性
    function calElement(lYear,lMonth,lDay,RnYue,aColor,bColor,cColor,JieQi,JieRi,Ganzhi){
        this.lYear=lYear;
        this.lMonth=lMonth;
        this.lDay=lDay;                    	//农历日数字
        this.RnYue=RnYue;                	//是否闰月
        this.aColor=aColor;                	//周六周日红字颜色
        this.bColor=bColor;                	//今天的背景色
        this.cColor=cColor;                	//公历节假日颜色
        this.JieQi=JieQi;                	//节气
        this.JieRi=JieRi;                	//农历节日
        this.Ganzhi=Ganzhi;                	//日期的天干地支搭配
        
    }
    //===== 某年的第n个节气为几日(从0小寒起算)
    function sTerm(y, n) {
        var offDate = new Date(( 31556925974.7 * (y - 1900) + sTermInfo[n] * 60000  ) + Date.UTC(1900, 0, 6, 2, 5));
        return(offDate.getUTCDate());
    }
}