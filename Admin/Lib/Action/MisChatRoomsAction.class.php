<?php
//Version 1.0
/**
 * 聊天室房间模块
 * @author arrowing
 * @time 2013-2-1
 */
class MisChatRoomsAction extends CommonAction {
	public function _filter(&$map){
		 if ($_SESSION["a"] != 1) $map['status'] = array("gt",-1);
    }
}
?>