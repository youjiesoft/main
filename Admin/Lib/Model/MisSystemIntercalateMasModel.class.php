<?php
class MisSystemIntercalateMasModel extends CommonModel{
	protected $trueTableName = 'mis_system_intercalate_mas';
	public $_auto	=array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
	);
}