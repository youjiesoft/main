<?php
class UcenterBbs {	
	private $dbConfig = array(
		'persistent' => false,
		'host' => 'localhost',
		'port' => '3306',
		'login' => 'root',
		'password' => 'qwertasdfg1000', 
		'database' => 'youbbbs'
	);
	/* 整合对象数据表前缀 */
	private $prefix         = 'yunbbs_';
	/* 数据库所使用编码 */
	private $charset        = 'utf8';
	/* 整合对象使用的cookie的domain */
	private $cookie_domain  = '61.142.7.36';
	/* 整合对象使用的cookie的path */
	private $cookie_path    = '/';
	/*------------------------------------------------------ */
	//-- PRIVATE ATTRIBUTEs
	/*------------------------------------------------------ */
    
	private $link;
 
	public function __construct() {
		$this->connect();
	}
	/**
	 * 链接数据库
	 * @return boolean
	 */
	private function connect() {
		$this->link = mysql_connect($this->dbConfig['host'].':'.$this->dbConfig['port'], $this->dbConfig['login'], $this->dbConfig['password'], true);	
		if ($this->link) {
			mysql_select_db($this->dbConfig['database'], $this->link);	
			mysql_query('SET NAMES UTF8', $this->link);	
			return true;	
		} else {
			return false;
		}
	}
	/**
	 * 根据用户名获取用户信息
	 * @param string $username 用户名
	 * @return array
	 */
	public function getUserByName($username = '',$erpid='') {
		$sql = "SELECT * FROM yunbbs_users WHERE name='".$username."' and erpid='".$erpid."' LIMIT 1";
		$res = mysql_query($sql, $this->link);
		if ($res === false) {
			return false;
		}
		
		$row = mysql_fetch_assoc($res);
		if($row) {
			return $row;
		} else {
			return false;
		}
	} 
	/**
	 * 用户登陆
	 * @param string $username 用户名
	 * @param string $password 密码
	 * @return boolean
	 */
	public function login($username = '',$erpid='', $password = '' ) {
		if (!$this->link) {
			$this->connect();
		}
		$row = $this->getUserByName($username,$erpid);
		if($row) {
			return $row;
			/*以下注释待优化,修改密码时应该同步修改，但是bbs不能单独登录所以忽略
			$pwmd5 = $password;
			if($pwmd5 == $row['password']) {
				return $row;
			} else {
				return false;
			}*/
		} else {
			return false;
		}	
	}
	/**
	 * 用户注册
	 * @param string $name 用户名
	 * @param int $flag 0禁用1等待审核5普通用户99管理员
	 * @param string $pwmd5 密码
	 * @param string $email 邮箱
	 * @param int $timestamp 注册时间
	 */
	public function register($name = '',$erpid='', $flag = 5, $password = '', $email = '', $timestamp = 0) {
		$pwmd5 = $password;
		$timestamp = time();
		$sql = "INSERT INTO yunbbs_users(name,erpid, flag, password, email, regtime) VALUES ('$name','$erpid', $flag, '$pwmd5', '$email', $timestamp)";
		$res = mysql_query($sql, $this->link);
		if ($res === false) {
			return false;
		}
		$id = mysql_insert_id($this->link);
		if ($id) {
			return self::login($name,$erpid, $password);
		}
		return false;
	}
	
	/**
	 * 获取最新帖子
	 * @param int $num 数量
	 * @return array
	 */
	public function getNewestBbs($num=10) {
		if($num<=0) return array();
		$query_sql = "SELECT a.id,a.uid,a.ruid,a.title,a.addtime,a.edittime,a.comments,a.visible,a.title1,a.title2,a.title3,a.title4,u.avatar as uavatar,u.name as author,ru.name as rauthor FROM yunbbs_articles a  LEFT JOIN yunbbs_users u ON a.uid=u.id LEFT JOIN yunbbs_users ru ON a.ruid=ru.id order by a.addtime desc LIMIT 0,".$num;
		$res = mysql_query($query_sql, $this->link);
		
		if ($res === false) {
			return array();
		} else {
			$newest = array();
			while ($row = mysql_fetch_assoc($res) ) {
				$newest[] = $row;
			}
			return $newest;
		}
	}
}
?>