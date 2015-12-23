<?php
/*
 * 外部收件箱功能
 *mis_hrmessage
 * */
class MisMessageInterEmailInboxAction extends CommonAction {
	public function _before_index(){
		$configEmailModel = D('MisSystemEmail');
		$map = array();
		$map['status'] = 1;
		$map['defaultemail'] = 1;
		$map['userid'] = $_SESSION[C('USER_AUTH_KEY')];
		$vo = $configEmailModel->where($map)->find();
		//连接外部邮箱
		$titlearr = $this->ReceiveEmail('jiang7552261@163.com','75522610826','jiang7552261@163.com','pop.163.com','pop3','110',false);
// 		print_r($titlearr);die;
		$iEmModel = D('MisMessageInterEmailInbox');
		$map = array();
		$map['createid'] = $_SESSION[C('USER_AUTH_KEY')];
		$map['toaddress'] = array('like','%'.'jiang7552261@163.com'.'%');//$vo['email']
		$dbmsgidarr = $iEmModel->where($map)->getField('message_id',true);
		//判断添加新邮件
		C ("TOKEN_ON", false);
		foreach ($titlearr as $key => $val) {
			if (!(in_array($val['message_id'], $dbmsgidarr))) {
				$_POST = array();
				$_POST['fromaddress'] = $val['createid'];//发件人
				$_POST['toaddress'] = $val['recipient'];//收件地址
				$_POST['toname'] = $_SESSION[C('USER_AUTH_KEY')];//收件人名称
				$_POST['toothaddress'] = $val['copytopeopleid'];//抄送人
				$_POST['title'] = $val['title'];//标题
				$_POST['totime'] = $val['emaildate'];//收件时间
				$_POST['message_id'] = $val['message_id'];//收件时间
				if (false === $iEmModel->create ()) {
					$this->error ( $iEmModel->getError () );
				}
				$re = $iEmModel->add();
				if (!$re){
					$this->error ( L('_ERROR_'));
				}
			}
		}
		C ("TOKEN_ON", true);
	}
	
	/* public function index(){
		$configEmailModel = D('MisSystemEmail');
		$map = array();
		$map['status'] = 1;
		$map['defaultemail'] = 1;
		$map['userid'] = $_SESSION[C('USER_AUTH_KEY')];
		$vo = $configEmailModel->where($map)->find();
		$titlearr = $this->ReceiveEmail('jiang7552261@163.com','75522610826','jiang7552261@163.com','pop.163.com','pop3','110',false);
		
		$this->assign('list',$titlearr);
		$this->display();
	} */
	/**
	 * ]@Title: lookupgetbody 
	 * @Description: todo(邮件的直接查看，收件箱右边显示的内容)   
	 * @author xiafengqin 
	 * @date 2013-9-3 下午3:01:32 
	 * @throws
	 */
	public function lookupgetbody(){
		$mid = $_REQUEST['mid'];
		//获取当前人的外部邮箱的相关配置
		$configEmailModel = D('MisSystemEmail');
		$this->assign('downdataid', $mid-1);//上一条
		$this->assign('updataid', $mid+1);//上一条
		if ($_REQUEST['isUpDown'] == 'prev'){
			$this->assign('downdataid', $mid+1);//上一条
			$mid = $mid-1;
		}
		if ($_REQUEST['isUpDown'] == 'next') {
			$this->assign('updataid', $mid+1);//下一条
			$mid = $mid+1;
		}
		$map = array();
		$map['status'] = 1;
		$map['defaultemail'] = 1;
		$map['userid'] = $_SESSION[C('USER_AUTH_KEY')];
		$vo = $configEmailModel->where($map)->find();
		//引入并实例化receivemail这个类
		import("@.ORG.Mailer.receivemail");
		$obj= new receiveMail('jiang7552261@163.com','75522610826','jiang7552261@163.com','pop.163.com','pop3','110',false);
		$obj->connect();         //If connection fails give error message and exit
		$tot=$obj->getTotalMails(); //Total Mails in Inbox Return integer value
		$mid = $tot - ($mid - 1);
		$head=$obj->getHeaders($mid);  // Get Header Info Return Array Of Headers **Array Keys are (subject,to,toOth,toNameOth,from,fromName)
		$new['title'] = $head['subject'];
		
		$new['recipient'] = $head['to']; //收件人
		$new['copytopeopleid'] = $head['toOth']; //抄送人
		$new['createid'] = $head['from']; //发件人
		$new['emaildate'] = $head['date']; //邮件接收时间
		$new['content'] = $this->test($obj->getBody($mid));;
		$str=$obj->GetAttach($mid,"./"); // Get attached File from Mail Return name of file in comma separated string  args. (mailid, Path to store file)
		$new['attr'] = explode(",",$str);
		$this->assign('default', $new);
		$this->display('lookupreadmessage');
	}
}
?>