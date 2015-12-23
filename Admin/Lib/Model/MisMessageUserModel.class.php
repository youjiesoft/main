<?php
/**
 * @Title: file_name
 * @Package package_name
 * @Description: todo(用户所有邮件模型)
 * @author liminggang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-8-29 下午5:07:10
 * @version V1.0
 */
class MisMessageUserModel extends CommonModel{
	protected $trueTableName = 'mis_message_user';
	public $_auto	=array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
	);
	/**
	 * @Title: getUserCurrentMsg 
	 * @Description: todo(首页登陆后，查询当前用户邮件信息) 
	 * @param int 参数为1或者2 $msg  如果msg为1，代表查询所有系统邮件，如果参数为2，分别查询系统邮件和不是系统邮件条数信息
	 * @author liminggang 
	 * @date 2014-8-29 下午5:12:26 
	 * @throws
	 */
	public function getUserCurrentMsg($msg){
		$msgmap['status'] = 1;
		$msgmap['commit'] = 1;
		$msgmap['readedStatus'] = 0;
		$msgmap['returnmessage']=1;//邮件未撤回
		$msgmap['recipient'] = $_SESSION [C ( 'USER_AUTH_KEY' )];
		if ($msg == 2) {
			//分开查询系统邮件和非系统邮件信息
			$msgmap['messageType']=1; //显示系统消息
			$msgarr['countSystemMessage'] = $this->where ( $msgmap )->count ( '*' );
			$a = $msgarr['countSystemMessage'];
			if ($msgarr['countSystemMessage'] > 99) {
				$msgarr['countSystemMessage'] = '99+';
			}
			if (!$msgarr['countSystemMessage']) {
				$msgarr['countSystemMessage'] = 0;
			}
			//取出收件箱内消息，数理
			$msgmap['messageType']=0; //不显示系统消息
			$msgarr['countInboxMessage'] = $this->where ( $msgmap )->count ( '*' );
			$b = $msgarr['countInboxMessage'];
			if ($msgarr['countInboxMessage'] > 99) {
				$msgarr['countInboxMessage'] = '99+';
			}
			if (!$msgarr['countInboxMessage']) {
				$msgarr['countInboxMessage'] = 0;
			}
			$msgarr['countsum'] = $a +$b;
			
			if ($msgarr['countsum'] > 99) {
				$msgarr['countsum'] = '99+';
			}
		}
		if ($msg == 1) {
			//综合一次性查询所有邮件信息
			$msgcount = $this->where($msgmap)->count("*");
			$msgarr=array("newmsg"=>$msgcount);
		}
		return $msgarr;
	}
}
?>