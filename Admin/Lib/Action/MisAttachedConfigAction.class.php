<?php
//Version 1.0
/**
 * Description of MisAttachedRecordAction
 *
 * @author mashihe
 */
class MisAttachedConfigAction extends CommonAction{
    /**
	 * @Title: _empty 
	 * @Description: todo(判断页面是否存在函数)   
	 * @author 杨东 
	 * @date 2013-5-31 下午3:59:09 
	 * @throws 
	*/  
    public function _empty(){
        //空操作
        $this->error("您访问的页面不存在！");
    }
    /**
     * @Title: ftp
     * @Description: todo(显示页面)
     * @author  
	 * @date 2013-5-31 下午3:59:09 
     */
    public function ftp(){
        $this->display();
    }
}
?>
