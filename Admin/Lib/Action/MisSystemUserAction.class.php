<?php
//Version 1.0
class MisSystemUserAction extends CommonAction{
	public function index() {
		//$name = $this->getActionName();
		$model=D('MisSystemUser');
        $list=$model->where('status>-1')->select();
        $this->assign("list",$list);
        $this->display();
	}
	 public function edit(){
		$name = $this->getActionName();
		$id = $_GET['id'];
		$map ['id'] = array('eq',$id);
		$model = D ($name);
		$list = $model->where($map)->find();
		$this->assign('vo',$list);
		$this->assign('clist',$list);
		$model=M('mis_system_duty');
	 	$list=$model->select();
	 	$this->assign('alist',$list);
	 	$model=M('mis_system_department');
	 	$list=$model->select();
	 	$this->assign('vlist',$list);
		$this->display();
	 }
	 public function _before_add(){
	 	$model=M('mis_system_duty');
	 	$list=$model->select();
	 	$this->assign('clist',$list);
	 	$model=M('mis_system_department');
	 	$list=$model->select();
	 	$this->assign('vlist',$list);
	 }
	 public function insert(){
           $model = M ('mis_system_user');
        $data=array(
            'manname'=>$_REQUEST['manname'],
            'sex'=> $_REQUEST['sex'],
            'workdate'=>strtotime($_REQUEST['workdate']),
            'dutyid'=>$_REQUEST['dutyid'],
            'remark'=>$_REQUEST['remark'],
            'createtime'=>strtotime($_REQUEST['createtime']),
            'createid'=>$_REQUEST['createid'],
            'updatetime'=>strtotime($_REQUEST['updatetime']),
            'updateid'=>$_REQUEST['updateid'],
            'email'=>$_REQUEST['email'],
            'deptid'=>$_REQUEST['deptid'],
            'status'=>$_REQUEST['status']
        );
        $model->data($data);
        //保存当前数据对象
        $list=$model->add($data);
        if ($list!==false) { //保存成功
                $this->success ( L('_SUCCESS_') );
        } else {
                $this->error ( L('_ERROR_') );
        }
	 }
    public function update(){
        $model = M ('mis_system_user');
        $data=array(
             'manname'=>$_REQUEST['manname'],
            'sex'=> $_REQUEST['sex'],
            'workdate'=>strtotime($_REQUEST['workdate']),
            'dutyid'=>$_REQUEST['dutyid'],
            'remark'=>$_REQUEST['remark'],
            'createtime'=>strtotime($_REQUEST['createtime']),
            'createid'=>$_REQUEST['createid'],
            'updatetime'=>strtotime($_REQUEST['updatetime']),
            'updateid'=>$_REQUEST['updateid'],
            'email'=>$_REQUEST['email'],
            'deptid'=>$_REQUEST['deptid'],
            'status'=>$_REQUEST['status']
        );
        $model->data($data);
        //保存当前数据对象
        $list=$model->where('id='.$_REQUEST['id'])->save();
        if ($list!==false) { //保存成功
                $this->success ( L('_SUCCESS_') );
        } else {
                $this->error ( L('_ERROR_') );
        }
    }
}
?>