<?php
/**
 * @Title: MisRollAnnouncement 
 * @Package package_name 
 * @Description: todo(滚动公告) 
 * @author libo 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-1-13 下午2:29:08 
 * @version V1.0
 */
class MisRollAnnouncementAction extends CommonAction{
	public function _filter(&$map){
		if(!isset($_SESSION['a'])){
			$map['status']=1;
		}
	}
	
}


?>