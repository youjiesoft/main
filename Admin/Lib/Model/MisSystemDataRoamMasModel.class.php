<?php
class MisSystemDataRoamMasModel extends CommonModel{
	protected $trueTableName = 'mis_system_data_roam_mas'; 
	private $system_field=array("departmentid"=>'部门id', "sysdutyid"=>"职级id", "projectid"=>"项目id", "projectworkid"=>"任务id","companyid"=>"公司id");
	
// 	public $_validate=array(
// 			array('orderno,status','','单号已经存在',self::MUST_VALIDATE,'unique',self::MODEL_BOTH),
// 			array('orderno','require','单号必须'),
// 	);
	//查不到对应表名的做一个数组特殊处理
	private $privatetables = array(
			'MisSaleMyBusiness'=>'mis_sale_business', //我的商机
			'MisSalesProjectAllocation'=>'mis_project_flow_resource',//项目分配
			'MisAutoVhz'=>'mis_auto_ixwla', //召集汇总
			'MisAutoUuo'=>'mis_auto_dzzvn',//评审汇总
				
	);
	public $_validate=array(
			//array('sourcemodel,targetmodel,isbindsettable','','同类型的漫游已做设置！',self::MUST_VALIDATE,'unique',self::MODEL_BOTH),
			array('sourcemodel','require','来源模型必须'),
			array('targetmodel','require','目标模型必须'),
	);
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
	 * @Title: getTableFieldForRoam
	 * @Description: todo(漫游专用，读取除开始内嵌表字段以外的表字段)+(补充：mis_dynamic_database_mas改为以modelname为条件查询)
	 * （为了改动小一点，函数名不变，改动参数，当传入第二个参数时，我用modelname查询mis_dynamic_database_mas，mis_dynamic_database_sub表，
	 * 否则就直接根据传入的第一个参数，查询真实物理表）
	 * @param unknown_type $tablename  
	 * @author 谢友志 
	 * @date 2015-4-13 下午8:27:46 
	 * @throws
	 */
	public function getTableFieldForRoam($tablename,$modelname=''){
		$tablefields = array();
		$privatetablearr = $this->privatetables;
		$privatemodelarr = array_keys($privatetablearr);
		if($modelname){
			if(in_array($modelname,$privatemodelarr)){
				$tablename = $privatetablearr[$modelname];
				$tablefields = $this->getTrueFields($tablename);
				return $tablefields;
			}
			$map['modelname'] = $modelname;
			$map['status'] = 1;
			$masmodel =  M("mis_dynamic_database_mas") ;
			$rs = $masmodel->where($map)->find();
			if($rs){
				//如果该表是动态拖出来的，则在mis_dynamic_database_sub查询类型不是datatable的字段
				$sub['masid'] = array('eq',$rs['id']);
				$sub['status'] = array('eq',1);
				$sub['category'] = array('neq','datatable');
				$tablefields = M("mis_dynamic_database_sub")->where($sub)->getField('field,title,type');
				if($tablefields){
					//系统字段
					require  CONF_PATH.'property.php';
					$all = array_merge($NBM_DEFAULTFIELD,$NBM_AUDITFELD,$NBM_AUDITFELD);
					$newall=array();
					foreach($all as $key=>$val){
						$newall[$key]['field']=$key;
						$newall[$key]['title']=$val['title'];
						$newall[$key]['type']=$val['type'];
					}
					$id['id']['field']='id';
					$id['id']['title']='ID';
					$id['id']['type']='int';
					//合并
					$tablefields = array_merge($id,$tablefields,$newall);
				}else{
					//如果mis_dynamic_database_sub没记录，直接查物理表
					$tablename = D($modelname)->getTableName();
					$tablefields = $this->getTrueFields($tablename);
				}
				
			}else{
				//如果mis_dynamic_database_mas没记录，直接查物理表
					$tablename = D($modelname)->getTableName();
				$tablefields = $this->getTrueFields($tablename);
			}			
		}else{
			//如果只传了表名，直接查物理表
			$tablefields = $this->getTrueFields($tablename);
		}		
		return $tablefields;
	}
	/**
	 * @Title: getTableFields
	 * @Description: todo(根据表名查真实物理表的字段)   
	 * @author 谢友志 
	 * @date 2015-4-16 上午10:00:02 
	 * @throws
	 */
	private function getTrueFields($tablename){
		//如果只传了表名，直接查物理表
		$tableObj=M("INFORMATION_SCHEMA.COLUMNS","","",1);
		$map2['TABLE_SCHEMA']=C('DB_NAME');
		$map2['TABLE_NAME'] = $tablename;
		$columnsList = $tableObj->where($map2)->getField("COLUMN_NAME,TABLE_NAME,COLUMN_KEY,EXTRA,IS_NULLABLE,DATA_TYPE,COLUMN_TYPE,COLUMN_DEFAULT,COLUMN_COMMENT");
		foreach($columnsList as $k=>$v){
			$tablefields[$k]['field'] = $v['COLUMN_NAME'];
			$tablefields[$k]['title'] = $v['COLUMN_COMMENT'];
			$tablefields[$k]['type'] = $v['DATA_TYPE'];
		}
		return $tablefields;
	}
}