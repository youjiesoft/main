<?php
/**
 * 页面布局设计Model
 * @Title: DesingFormModel 
 * @Package package_name
 * @Description: todo(用一句话描述该类的作用) 
 * @author quqiang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015年5月8日 下午4:10:34 
 * @version V1.0
 */
class DesingFormModel extends CommonModel{
	/**
	 * 	真实表名
	 * @var string
	 */
	protected $trueTableName = 'mis_system_design_form';
	
	/**
	 * 自动填充
	 * @var array
	 */
	public $_auto	=array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('status','1'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
	);
	/**
	 * 自动验证
	 * @var array
	 */
	public $_validate	=	array(
			array('actionname','','对象重复，请检查！',self::MUST_VALIDATE,'unique',self::MODEL_BOTH),//多字段组合验证
	);
}