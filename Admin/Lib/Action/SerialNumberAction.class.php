<?php
//Version 1.0
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Description of SerialNumberAction
 *
 * @author Administrator
 */
class SerialNumberAction extends Action{
    //put your code here
    public function index() {
        $model=D("Serialnumber");
        $list=$model->Authorize();
        $this->assign("list", $list);
        $this->display();
    }
    public function check(){
        $name=$this->getActionName();
        $model=D($name);
        $key=$_REQUEST['key'];
        if($model->remote($key)){
            redirect(U("Public/login"));
        }else{
            redirect(U("Public/serialnumber"));
        }
    }
    public function sucess(){
        $this->display();
    }
}
?>