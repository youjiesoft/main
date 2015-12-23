<?php
/**
 * @Title: MisNewsAction
 * @Package package_name
 * @Description: todo(公司新闻)
 * @author laicaixia
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-5-31 下午4:50:55
 * @version V1.0
 */
class MisNewsAction extends CommonAction{
	//put your code here
	/**
	* @Title: _filter
	* @Description: todo(检索)
	* @param unknown_type $map
	* @author laicaixia
	* @date 2013-5-31 下午4:51:38
	* @throws
	*/
	public function _filter(&$map){
		$map['type']="company";
		$map['status']=1;
	}
	/**
	 * @Title: _before_add
	 * @Description: todo(进入新增)
	 * @author laicaixia
	 * @date 2013-5-31 下午4:51:46
	 * @throws
	 */
	public function _before_add(){
		$model=D('User');
		$list=$model->where('status = 1')->getField("id,name");
		$this->assign("ulist", $list);
		$time=time();
		$this->assign("time", $time);
	}
	/**
	 * @Title: _before_edit
	 * @Description: todo(进入修改)
	* @author laicaixia
	* @date 2013-5-31 下午4:51:56
	* @throws
	*/
	public function _before_edit(){
		$model=D('User');
		$list=$model->where('status = 1')->getField("id,name");
		$this->assign("ulist", $list);
		$time=time();
		$this->assign("time", $time);
	}
	/**
	 * @Title: appview
	 * @Description: todo(暂时没有用)
	* @author laicaixia
	* @date 2013-5-31 下午4:52:42
	* @throws
	*/
	private function appview(){
		$id=$_GET['id'];
		$model=D("CompanyNews");
		$list=$model->where("id=".$id)->find();
		$this->assign("vo",$list);
		$this->display();
	}
	/** 
	 * @Title: app
	 * @Description: todo(这里用一句话描述这个方法的作用)
	* @author laicaixia
	* @date 2013-5-31 下午4:52:44
	* @throws
	*/
	private function app(){
		$model=D("SystemAnnouncement");
		$map['type']="company";
		$list=$model->where($map)->order("createtime desc")->limit(5)->select();
		$this->assign("list",$list);
		$this->display();
	}
}

?>
