<?php
//Version 1.0
/**
 * @Title: EmailAction 
 * @Package package_name
 * @Description: todo(邮件发送设置) 
 * @author laicaixia
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-6-2 上午10:24:32 
 * @version V1.0 
*/ 
class EmailAction extends CommonAction {
	/* (non-PHPdoc)列表
	 * @see CommonAction::index()
	 */
	public function index(){
		$type = D('Email_tmp_type');
		$tmps = $type->where('status=1')->select();
		$set = D('Email_set');
		$userid = $_SESSION[C('USER_AUTH_KEY')];
		$result = $set->where("userid=".$userid)->field('empobject,info')->find();
		if(!empty($result) && is_array($result)){
			$info = explode(';', $result['info']);
			$select = array();
			for($i=0;$i<count($info);$i++){
				$con = explode(':', $info[$i]);
				$fs = explode(',', $con[1]);
				$select[$con[0]] = $fs;
			}
		}
		$tmp_type_name = array();
		$tmp_type_name[0] = '全局地址';
		$count = count($tmps);
		for($i=0;$i<$count;$i++){
			$tmp_type_name[$tmps[$i]['id']] = $tmps[$i]['name'];
			$fields = $tmps[$i]['field'];
			$fields = explode('|', $fields);
			$field_names = $tmps[$i]['field_name'];
			$field_names = explode('|', $field_names);
			$len = count($fields);
			for($j=0;$j<$len;$j++){
				$tmps[$i]['fields'][$j]['field'] = $fields[$j];
				$tmps[$i]['fields'][$j]['field_name'] = $field_names[$j];
				if(in_array($fields[$j], $select[$tmps[$i]['type']])){
					$tmps[$i]['fields'][$j]['check'] = 'checked';
				}
			}
		}
		$empobject = $result['empobject'];
		$this->assign('empobject', $empobject);
		if(!empty($empobject) && $empobject != ''){
			$all_address = explode(',', $result['empobject']);
			$count_address = count($all_address);
			$addresses = array();
			foreach($all_address as $key => $address){
				$thisaddress = explode('#', $address);
				$typeid = $type->where('type='.$thisaddress[1])->getField('id');
				$type_index = $thisaddress[1] == 0 ? 0 : $typeid;
				$key += $count_address*intval($typeid); //根据tmp_type排序，从小到大
				$addresses[$key]['db_address'] = $address;
				$addresses[$key]['tmp_type'] = $tmp_type_name[$type_index];
				$addresses[$key]['email'] = $thisaddress[0];
			}
			ksort($addresses);
			$this->assign('addresses', $addresses);
		}
		$this->assign('tmps', $tmps);
		$this->display();
	}
	/**
	 * @Title: save 
	 * @Description: todo(保存)   
	 * @author laicaixia 
	 * @date 2013-6-2 上午10:24:59 
	 * @throws 
	*/  
	public function save(){
		$addresses = $_POST['addresses'];
		$tmp_type = $_POST['tmp_type'];
		$data['userid'] = $_SESSION[C('USER_AUTH_KEY')];
		$data['empobject'] = $addresses;
		$info = '';
		for($i=0;$i<count($tmp_type);$i++){
			$fields = $_POST['field_'.$tmp_type[$i]];
			$fields = implode(',', $fields);
			$info .= $tmp_type[$i].':'.$fields.';';
		}
		$data['info'] = substr($info, 0, -1);
		$dao = D('Email_set');
		$int = $dao->where("userid=".$data['userid'])->getField('userid');
		if($int == $data['userid']){
			$list = $$dao->where("userid=$int")->save($data);
		}else{
			$list = $dao->add($data);
		}
		if( false!== $list ){
			$this->success( L('_SUCCESS_') );
		}else{
			$this->error( L('_ERROR_') );
		}

		//echo $this->ajaxCallBack('Email');
	}
	/* (non-PHPdoc)预览
	 * @see Action::show()
	 */
	public function show(){
		$role = D('Role');
		$user = D('User');
		$role_user = D('Role_user');
		$roles = $role->where("status=1")->field('id,name')->select();
		$users = array();
		foreach($roles as $k => $role){
			$users[$k]['department'] = $role['name'];
			$user_ids = $role_user->where("role_id=".$role['id'])->field('user_id')->select();
			foreach($user_ids as $user_id){
				$users[$k]['users'] = $user->where("status=1 and id=".$user_id['user_id'])->field('name,email')->select();
			}
		}
		$this->assign('users', $users);
		$this->display();
	}
	/**
	 * @Title: test 
	 * @Description: todo(测试)   
	 * @author laicaixia 
	 * @date 2013-6-2 上午10:25:25 
	 * @throws 
	*/  
	private function test(){
		$subject = $_POST['subject'];
		$body = $_POST['body'];
		$type = $_POST['type'];
		$orderno = $_POST['orderno'];
		echo $this->send($subject, $body, $type, $orderno);
	}
	/**
     +----------------------------------------------------------
	 * 邮件发送
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @param string $subject 邮件主题
	 * @param string $body 邮件内容
	 * @param string $type 模板类型
	 * @param string $orderno 订单编号
	 * @param array  $addresses 发送地址,英文逗号分隔
	 * @param array  $departments 部门id列表
	 * @param array  $attachments 附件列表
     +----------------------------------------------------------
	 * @return  string
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	private function send($subject, $body, $type = false, $orderno = '',$addresses = '', $departments = false, $attachments = array()){
		if($type){
			$set = D('Email_set');
			$own_addresses = $set->where("userid=".$_SESSION[C('USER_AUTH_KEY')])->getField('empobject');
			$own_addresses = explode(',', $own_addresses);
			$thistmp_addresses = array();
			foreach($own_addresses as $own_address){
				$own_address = explode('#', $own_address);
				if($own_address[1] == $type || $own_address[1] == 0)
					$thistmp_addresses[] = $own_address[0];
			}
		}else{
			$thistmp_addresses = array();
		}
		$addresses = explode(',', $addresses);
		if($departments){
			$role = D('Role_user');
			$user = D('User');
			foreach($departments as $departmentid){
				$user_role = $role->where("role_id=".$departmentid)->select();
				foreach($user_role as $userid){
					$useremail = $user->where('id='.$userid['user_id'])->getField('email');
					array_push($thistmp_addresses, $useremail);
				}
			}
		}
		import("@.ORG.Mailer.PHPMailer");
		$mail = new PHPMailer();
		$mail->CharSet = "UTF-8";
		$mail->IsSMTP(); // telling the class to use SMTP
		//$mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
		                                           // 1 = errors and messages
		                                           // 2 = messages only
		$mail->SMTPAuth   = true;                  // enable SMTP authentication
		$mail->Host       = C('SMTP_HOST');         // sets the SMTP server
		$mail->Port       = C('SMTP_PORT');        // set the SMTP port for the GMAIL server
		$mail->Username   = C('EMAIL_USERNAME');    // SMTP account username
		$mail->Password   = C('EMAIL_PASSWORD');     // SMTP account password
		$mail->Subject    = $subject;
		$body = eregi_replace("[\]",'',$body);
		$mail->MsgHTML($body);
		$mail->SetFrom(C('EMAIL_SERVERADDRESS'),C('EMAIL_SERVERNAME'));
		$addresses = array_unique(array_merge($addresses, $thistmp_addresses));
		foreach($addresses as $address){
			//邮址格式 称呼:邮址
			$address = explode(':', $address);
			if(count($address) == 2){
				$mail->AddAddress($address[1], $address[0]);
			}else{
				$mail->AddAddress($address[0]);
			}
		}
		foreach($attachments as $attachment){
			$mail->AddAttachment($attachment);
		}
		if(!$mail->Send()) {
		  	return "发送失败，请联系管理员"; //$mail->ErrorInfo
		} else {
			$record = D('Email_send_record');
			$tmptype = D('Email_tmp_type');
			$data['type'] = $type;
			$data['orderno'] = $orderno;
			$data['userid'] = $_SESSION[C('USER_AUTH_KEY')];
			$data['stime'] = time();
			$data['sendobj'] = implode(',', $addresses);
			$list = $record->add($data);
			if( false!== $list ){
				$this->success( L('_SUCCESS_') );
			}else{
				$this->error( L('_ERROR_') );
			}
		}
	}
}
?>