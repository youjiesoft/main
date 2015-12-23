<?php
/**
 * @Title:发送消息类
 * @Package 基类
 * @Description: 主要应用于审批意见，对某人发送消息
 * @author arrowng
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013年1月16日
 * @version V1.0
 */
class SendMsgAction extends CommonAction{

	function index(){

		$userid = $_GET['userid'];
		$name = getFieldBy($userid, 'id', 'name', 'user');

		$this->assign('name', $name);
		$this->assign('userid', $userid);
		$this->display();
	}

	function send(){
		$type = $_POST['type'];
		$re = false;

		switch($type){
			case '1' ://站内方式
				$re = $this->pushMessage(array($_POST['userid']), $_POST['title'], $_POST['content']);
			break;
			case '2' ://短信方式
				$mobile = D('User')->where('id='.$_POST['userid'])->getField('mobile');
				if(is_numeric($mobile)){
					$re = $this->SendTelmMsg($_POST['content'], $mobile);
				}else{
					$this->error('手机信息错误！');
				}
			break;
			case '3' ://邮件方式
				$email = D('User')->where('id='.$_POST['userid'])->getField('email');
				if($email){
					$re = $this->SendEmail($_POST['title'], $_POST['content'], $email);
				}else{
					$this->error('邮箱信息错误！');
				}
			break;
		}

		$this->success("发送成功");
	}
}
?>