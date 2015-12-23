<?php
/**
 * 动态面板基类
 * @Title: AutoPanelAction 
 * @Package package_name
 * @Description: todo(用一句话描述该类的作用) 
 * @author quqiang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015年5月8日 下午3:45:40 
 * @version V1.0
 */
abstract class AutoPanelAction extends CommonAction{
	protected $id='';
	/**
	 * 当前配置内容
	 * @var array
	 */
	protected $config;
	
	/**
	 * 当前面板类型，1：报表，2：新闻，3：普通数据
	 * @var int
	 */
	protected $type;
	
	/**
	 * 数据类型，1：站内数据，2：外部url，3：内部url，4：sql
	 * @var int
	 */
	protected $dataType;
	
	/**
	 * 当前面板标题
	 * @var string
	 */
	protected $curPanelTitle;
	
	/**
	 * 当前面板Action名称
	 * @var string
	 */
	protected $curPanelName;
	
	function __construct(){
		parent::__construct();
	}
	/**
	 * 属性设置
	 * @Title: setting
	 * @Description: todo(这里用一句话描述这个方法的作用)   
	 * @author quqiang 
	 * @date 2015年5月8日 下午3:46:29 
	 * @throws
	 */
	abstract public function setting();
	
	/**
	 * 显示当前面板内容
	 * @Title: showPanel
	 * @Description: todo(这里用一句话描述这个方法的作用)   
	 * @author quqiang 
	 * @date 2015年5月8日 下午3:46:50 
	 * @throws
	 */
	abstract public function showPanel();
	
	/**
	 * 获取配置内容
	 * @Title: getConfig
	 * @Description: todo(这里用一句话描述这个方法的作用)面板禁权时不显示判断 by xyz 2015-11-05   
	 * @author quqiang 
	 * @date 2015年5月8日 下午3:47:31 
	 * @throws
	 */
	public function getConfig(){
		$model = D("MisSystemPanelDesingMas");
		$userid = $_SESSION[C("USER_AUTH_KEY")];
		$status = $model->getForbitRoleOfPanel($userid,$this->id);
		if($status){
			return "display:none;";
		}else{
			return "";
		}
	}
}