<?php
/**
 * @Title: MisNoticeModel
 * @Package package_name
 * @Description: todo(公司公告)
 * @author 杨东
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-7-4 下午5:49:35
 * @version V1.0
 */
class MisNoticeModel extends Model{

	/**
	 * @Title: getNoticeList
	 * @Description: todo(获取公司公告新闻) 
	 * @return string|multitype:  
	 * @author 杨东 
	 * @date 2013-7-4 下午5:52:32 
	 * @throws 
	*/  
	public function getNoticeList(){
		$file = UPLOAD_PATH."tml_news.htm";//filemtime($file)<time()-3600*24
		if (filemtime($file)<time()-3600*24) {
			$url="http://www.966580.com/?q=main_list";
			$s=url_get_contents($url);
			$s = mb_convert_encoding( $s, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5' );
			//print_r($s);
			file_put_contents($file,$s);
			if ($s=='') {
				return '';
			} else{
				$s=get_tag_data($s,'<div class="listbox">','</div>');
			}
	
		    file_put_contents($file,$s);
			//print_r($s);
		} else {
			$s = file_get_contents($file);
		}
		preg_match_all('/<a.*?(?: |\\t|\\r|\\n)?href=[\'"]?(.+?)[\'"]?(?:(?: |\\t|\\r|\\n)+.*?)?>(.+?)<\/a.*?>/sim',$s,$m);
		preg_match_all("/<font.*?>(.+?)<\/font.*?>/",$s,$m1);
		/*preg_match_all('/<span.*?>(.+?)<\/span.*?>/',$s,$m1);*/
		$list = array();
		foreach($m[1] as $key=>$val){
			$list[$key]['url']="http://www.966580.com/".$val;
			//$list[$key]['url']="http://www.966580.com/".$val;
			$list[$key]['title']=$m[2][$key];
			//$list[$key]['date']=$m1[0][$key];
			//$list[$key]['date']=$m1[1][$key];
			if($key == 7){
				break;
			}
		}
		return $list;
	}
}
?>