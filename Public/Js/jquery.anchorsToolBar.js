;(function($){
    $.fn.anchorsToolBar = function(options){
        $.fn.anchorsToolBar.defaultSettings = {
            box:navTab.getCurrentPanel(),
            pDivCalssTag:'pageFormContent',         //滚动条DIV层
            anchors:'side-catalog-anchor',          //锚点
            textClassTag:'side-catalog-anchor',     //获取导航文字的标签列
            firstAnchors:'side-catalog-firstanchor' //第一个锚点位置
        };
        var settings = $.extend({}, $.fn.anchorsToolBar.defaultSettings, options);
        
        return this.each(function(){
            var $box = settings.box;
            var isscroll = false;

            //绑定--滚动事件
            $box.find("."+settings.pDivCalssTag).bind("scroll", function(){
                optionDivRefresh();
            });
            //绑定--控制高航菜单display状态
            $(document).on("click","a.side-catalog-btn-disabled", function(){
                togglediv(this);
            });
            //绑定--返回顶部
            $(document).on("click","a.side-catalog-up", function(){
                scrollbarposition(this);
               
            });
            //绑定--滚动条定位操作
            //$box.find("."+settings.pDivCalssTag).find(".side-catalog-item").bind("click", function(){
            //    scrollbarposition(this);
            //});
            $(document).on("click","div.side-catalog-item", function(){
                scrollbarposition(this);
            });
            
            var optionDivRefresh = function(){
                var $box = settings.box;
                //遍历锚点 
                var mds = $box.find("."+settings.anchors);
                var arrMd = [];
                var len = mds.length;
                for(var i = 0; i < len; i++){
                    if ($(mds[i]).is(":visible")) {
                        arrMd.push($(mds[i]));
                    }
                }
                //滚动条的当前位置
                var scrollH = $box.find("."+settings.pDivCalssTag).scrollTop();
               //初始父节点到视窗的位置
               var scrollHInIt = $box.find("."+settings.pDivCalssTag).position().top;
               //第一个锚点相对父节点便宜位置
               var firstscrolloption = $box.find("."+settings.textClassTag).first().position().top;
               //var firstscrolloption = $box.find("."+settings.firstAnchors).position().top;
                var html = '<div class="side-catalog">\
                                <div class="side-catalog-bar">\
                                    <div class="side-catalog-top"></div>\
                                    <div class="side-catalog-bottom"></div>\
                                </div>\
                                <div class="side-catalog-list">';
                html += '<div class="side-catalog-box">';
                var i = 0;
                var subNodeIndex = 1;  // 二级节点序号
                var subNodeCount = 0;  // 二级节点总数

                var lastmdHeight = 0;  // 上一个锚点位置
                var nextmdHeight = 0;  // 下一个锚点位置
                $box.find("."+settings.anchors).each(function(){
                    if($(this).is(":visible")){
                        //单个锚点相对父节点的偏移量 + 鼠标位置=锚点相对位置
                        var mdHeight = arrMd[i].position().top + scrollH;
                        //截取锚点文字10个长度
                        var textval = $(this).find("."+settings.textClassTag).text().trim().slice(0,5);
                        if (isscroll === false) {
                            var index = 'index' + (i+1);

                            // 如果该节点是二级节点 则添加 class名 side-catalog-item2
                            if($(this).hasClass('side-catalog-anchor2')){
                                html += '<div class="side-catalog-item side-catalog-item2 navonindex'+(i+1)+'">\
                                        <span class="side-catalog-index">' + (i - subNodeCount + '.' + subNodeIndex)+'</span>\
                                        <a href="javascript:;" mdHeight="'+mdHeight+'">'+textval+'</a>\
                                        <span class="side-catalog-dot"></span>\
                                    </div>';
                                subNodeIndex++;
                                subNodeCount++;
                            } else {
                            html += '<div class="side-catalog-item navonindex'+(i+1)+'">\
                                        <span class="side-catalog-index">'+(i - subNodeCount + 1)+'</span>\
                                        <a href="javascript:;" mdHeight="'+mdHeight+'">'+textval+'</a>\
                                        <span class="side-catalog-dot"></span>\
                                    </div>';
                                subNodeIndex = 1;
                                }
                        }
                        //上一个锚点相对父节点的偏移量 + 鼠标位置=锚点相对位置
                        if (i != 0 ) {
                            lastmdHeight = arrMd[i-1].position().top +scrollH;
                        }
                        //下一个锚点相对父节点的偏移量 + 鼠标位置=锚点相对位置
                        if ((i + 1) < arrMd.length ) {
                            nextmdHeight = arrMd[i+1].position().top + scrollH;
                        }
                        if((lastmdHeight < scrollH +(mdHeight- lastmdHeight)/2) && (scrollH  + (nextmdHeight- mdHeight)/2 < nextmdHeight)){
                            navonHiLite(i);
                        }
                        i++;
                    }
                });
                if (isscroll === false) {
                    html += '</div></div></div>\
                                <a class="side-catalog-btn side-catalog-btn-disabled">隐藏/显示</a>\
                                <a class="side-catalog-up" href="#" mdHeight="0" title="返回顶部">返回顶部</a>';
                    $box.find(".sideToolbar").empty();
                    $box.find(".sideToolbar").append(html); 
                }
                isscroll = true;
                displaydiv(scrollH, firstscrolloption);
                $box.find(".sideToolbar").css('top', 60 + scrollH);
                
            }
            //高亮导航菜单
            var navonHiLite = function(id){
                $box.find('.sideToolbar').find(".side-catalog-item").removeClass('active');
                $box.find('.sideToolbar').find(".navonindex"+id).addClass('active');
                sidePosition(id);
            }
            //隐藏导航菜单
            var displaydiv = function(scrollH, firstscrolloption) {
                if (scrollH <= firstscrolloption) {
                    $box.find(".sideToolbar").empty();
                    isscroll = false;
                }
            }
        });
        //控制高航菜单display状态
        function togglediv(obj) {
            var $box = settings.box;
            $box.find(".side-catalog").toggle();
            $(obj).toggleClass("disabled");
            
        }
        //滚动条定位
        function scrollbarposition(obj){
            var $box = settings.box;
            var position = $(obj).find("a").attr("mdHeight");
            if (!position) {
                position = 0;
            }
            $box.find("."+settings.pDivCalssTag).scrollTop(position);
            $box.find("div.side-catalog-item").removeClass("active");
            setTimeout(function(){
                $box.find("div.side-catalog-item").removeClass("active");
                $(obj).addClass("active");
                var index = parseInt($(obj).attr('className').split('navonindex')[1]);
                sidePosition(index);
            }, 20);
            //滚动条的当前位置
            var scrollH = $box.find("."+settings.pDivCalssTag).scrollTop();
            $box.find(".sideToolbar").css('top', 60 + scrollH);
        }

        // 侧边导航滚动
        function sidePosition(index) {
            var $box = settings.box;
            var catalogBox = $box.find('.side-catalog-box');
            var catalogCount = $box.find('.side-catalog-item').length;
            if(index >= 5 && index <= (catalogCount - 4)) {
                catalogBox.css('top', ((5 - index) * 25) + 'px');
            }
        }
    }
})(jQuery);