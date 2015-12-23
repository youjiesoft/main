<?php
/**
 * @Title: MisNoticeAction 
 * @Package package_name
 * @Description: todo(官方动态) 
 * @author laicaixia
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-6-2 上午9:40:05 
 * @version V1.0 
*/ 
class MisNoticeAction extends CommonAction{
    /**
     * @Title: app 
     * @Description: todo(暂时没有用)   
     * @author laicaixia 
     * @date 2013-6-2 上午9:40:17 
     * @throws 
    */  
    private function app(){
		//得到官方动态信息
		$file=UPLOAD_PATH."tml_news.htm";
         if (filemtime($file)<time()-3600*24) {
         	$s=get_tmlnews($file);
         }
         else {$s=file_get_contents($file);}
        preg_match_all('/<a.*?(?: |\\t|\\r|\\n)?href=[\'"]?(.+?)[\'"]?(?:(?: |\\t|\\r|\\n)+.*?)?>(.+?)<\/a.*?>/sim',$s,$m);
        foreach($m[0] as $key=>$val){
			$list[$key]['url']="http://yun.tmlsoft.com/".$m[1][$key];
			$list[$key]['title']=$m[2][$key];
        }
        $this->assign("list", $list);
	    $this->display();
	}
}
?>