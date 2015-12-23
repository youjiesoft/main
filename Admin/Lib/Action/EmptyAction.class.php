<?php
/*
 * @author 王昭侠
 * @date 2015-03-11
 * @desc 空模块404等错误
 * */
class EmptyAction extends CommonAction {  
    public function _initialize(){  
         parent::_initialize();  
    }  
      
    public function index() {  
        $this->_empty();  
    }  
      
    public function _empty() {  
//         header('HTTP/1.1 404 Not Found');  
//         $this->display('Public:404');  
    }  
}  
?>