<?php
if (!defined('IN_ET')) exit();

class map_action {

    function __construct(&$pluginManager) {

    }

    function page() {
        if (Action::$site_info['pubusersx']==1) {
            $condition=" AND user_head!='' AND live_city!='' AND live_city!='其他 其他' AND user_gender!='' AND user_info!='' AND auth_email='1'";
        } else {
            $condition=" AND live_city!='' AND live_city!='其他 其他'";
        }

        $content = D('ContentView')->where('replyid=0'.$condition)->order("`content_id` DESC")->limit('30')->select();

        $result.='
        <style>
        .maps{width:300px;}
        .maps .link{width:50px;padding:4px;height:42px;float:left}
        .maps .link img {width:42px;height:42px;border:1px solid #cccccc;padding:1px}
        .maps .body{float:left;width:240px;}
        .maps .nickname {width:42px;overflow:hidden;height:16px;text-align:center}
        .maps .other{color:#cccccc}
        </style>
        <div style="position:absolute;width:100%;left:0;height:100%;top:0" id="map_container"></div>
        <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false" charset="utf-8"></script>';
        $result.='<script type="text/javascript">
            var m_city=new Array();
            var m_header=new Array();
            var m_content=new Array();';
        $lasttime=0;
        foreach($content as $key=>$val){
            $result.="m_city[$key]='".str_replace('海外 ','',$val['live_city'])."';";
            $result.="m_header[$key]='".sethead($val['user_head'])."';";
            $result.="m_content[$key]='<div class=\"maps\"><a href=\"".SITE_URL."/$val[user_name]\" title=\"$val[nickname]\" target=\"_blank\" class=\"link\"><img src=\"".sethead($val['user_head'])."\" alt=\"$val[nickname]\"/></a><span class=\"body\"><span class=\"nickname ".setvip($val['user_auth'])."\"><a href=\"".SITE_URL."/$val[user_name]\" title=\"$val[nickname]\" target=\"_blank\">$val[nickname]</a></span> ".daddslashes(D('Content')->outubb($val['content_body']))."<div class=\"other\">".timeop($val['posttime'])." $val[live_city]</div></span></div>';";
            $lasttime=max($val['posttime'],$lasttime);
        }
        $result.='</script>
        <script type="text/javascript">
        $(document).ready(function(){
            function getTotalHeight(){
                if($.browser.msie){
                    return document.compatMode == "CSS1Compat"? document.documentElement.clientHeight : document.body.clientHeight;
                } else {
                    return self.innerHeight;
                }
            }
            function getTotalWidth(){
                if($.browser.msie){
                    return document.compatMode == "CSS1Compat"? document.documentElement.clientWidth : document.body.clientWidth;
                } else{
                    return self.innerWidth;
                }
            }
            $(window).resize(function(){
                $("#map_container").width(getTotalWidth()+"px");
                $("#map_container").height(getTotalHeight()+"px");
            });
            $(".bottomLinks").remove();
            $("#map_container").width(getTotalWidth()+"px");
            $("#map_container").height(getTotalHeight()+"px");
            var myLatlng = new google.maps.LatLng(39.904214, 116.40741300000002);
            var myOptions = {zoom: 7,center: myLatlng,mapTypeId: google.maps.MapTypeId.ROADMAP,disableDefaultUI: true,disableDoubleClickZoom:true,scrollwheel:false}
            var map = new google.maps.Map(document.getElementById("map_container"), myOptions);
            var shadow = new google.maps.MarkerImage("'.SITE_URL.'/Plugin/map/images/map_avatar_bg.png",new google.maps.Size(58, 58),new google.maps.Point(0,0),new google.maps.Point(0,0));
            var infowindow = new google.maps.InfoWindow({content:"",maxWidth:400});
            var marker = new google.maps.Marker({map: map,shadow: shadow});
            var geocoder = new google.maps.Geocoder();
            var mlength=m_city.length;
            var flag=0;
            var lasttime = '.$lasttime.';
            function warp(){
                if (flag<mlength) {
                    geocoder.geocode( { "address": m_city[flag]}, function(results, status) {
                      if (status == google.maps.GeocoderStatus.OK) {
                        map.panTo(results[0].geometry.location);
                        infowindow.setContent(m_content[flag]);
                        var image = new google.maps.MarkerImage(m_header[flag],new google.maps.Size(32, 32),new google.maps.Point(0,0),new google.maps.Point(0,0),new google.maps.Size(32, 32));
                        marker.setIcon(image);
                        marker.setPosition(results[0].geometry.location);
                        infowindow.open(map,marker);
                      }
                      flag++;
                    });
                } else {
                    $.get("'.SITE_URL.'/p/map?action=getcontent&lasttime="+lasttime,
                    function(msg){
                        var stdata=jQuery.parseJSON(msg);
                        if (stdata!=null) {
                            if (stdata.res=="success") {
                                lasttime=stdata.lasttime;
                                m_city=stdata.result1;
                                m_header=stdata.result2;
                                m_content=stdata.result3;
                                mlength=m_city.length;
                                flag=0;
                            }
                        }
                    });
                }
                setTimeout(warp,5000);
            }
            if (mlength>0) {
                warp();
            }
        });
        </script>';
        return $result;
    }

    public function getcontent() {
        if (Action::$site_info['pubusersx']==1) {
            $condition=" AND user_head!='' AND live_city!='' AND live_city!='其他 其他' AND user_gender!='' AND user_info!='' AND auth_email='1'";
        } else {
            $condition=" AND live_city!='' AND live_city!='其他 其他'";
        }

        $lasttime=$_GET['lasttime'];
        $content = D('ContentView')->where('replyid=0'.$condition.' AND posttime>'.$lasttime)->order("`content_id` DESC")->limit('30')->select();
        if ($content) {
            foreach($content as $key=>$val){
                $result1[]=str_replace('海外 ','',$val['live_city']);
                $result2[]=sethead($val['user_head']);
                $result3[]="<div class=\"maps\"><a href=\"".SITE_URL."/$val[user_name]\" title=\"$val[nickname]\" target=\"_blank\" class=\"link\"><img src=\"".sethead($val['user_head'])."\" alt=\"$val[nickname]\"/></a><span class=\"body\"><span class=\"nickname ".setvip($val['user_auth'])."\"><a href=\"".SITE_URL."/$val[user_name]\" title=\"$val[nickname]\" target=\"_blank\">$val[nickname]</a></span> ".daddslashes(D('Content')->ubb($val['content_body']))."<div class=\"other\">".timeop($val['posttime'])." $val[live_city]</div></span></div>";
                $lasttime=max($val['posttime'],$lasttime);
            }
            echo json_encode(array('res'=>'success','result1'=>$result1,'result2'=>$result2,'result3'=>$result3,'lasttime'=>$lasttime));
        }
    }

    public function install() {
        return true;
    }

    public function uninstall() {
        return true;
    }
}
?>