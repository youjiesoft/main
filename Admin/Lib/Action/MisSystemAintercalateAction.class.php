<?php
 /**
  * 
  * @Title: MisSystemAintercalateAction 
  * @Package package_name
  * @Description: todo(管理员面板设置) 
  * @author renling 
  * @company 重庆特米洛科技有限公司
  * @copyright 本文件归属于重庆特米洛科技有限公司
  * @date 2015年5月21日 下午3:05:11 
  * @version V1.0
  */
class MisSystemAintercalateAction extends CommonAction{ 
	public function index(){
		//查询设置的快捷按钮
		$MisSystemIntercalateMasModel=M("mis_system_intercalate_mas");
		$MisSystemIntercalateSubModel=M("mis_system_intercalate_sub");
		$MisSystemIntercalateMasList=$MisSystemIntercalateMasModel->where("status=1")->select();
		$MisSystemIntercalateSubList=$MisSystemIntercalateSubModel->where("status=1")->select();
		$this->assign("MisSystemIntercalateMasList",$MisSystemIntercalateMasList);
		$this->assign("MisSystemIntercalateSubList",$MisSystemIntercalateSubList);
		$this->display();
	}
	public function cleartable(){
		
		$model=D("User");
		$sql="SELECT  tablename,formid FROM  `mis_dynamic_form_manage` LEFT JOIN `mis_dynamic_database_mas` ON  mis_dynamic_form_manage.id=mis_dynamic_database_mas.formid  WHERE tpl  NOT LIKE	'%basisarchivestpl%'  order by formid desc ";
		$list=$model->query($sql);
		$endsql="";
		$misDynamicFormProperyDao=M("mis_dynamic_form_propery");
		$endresult=true;
		$msg="";
		foreach ($list as $key=>$val){
			if($val['tablename']){
				$proMap=array();
				$proMap['category']="datatable";
				$proMap['formid']=$val['formid'];
				$datelist=$misDynamicFormProperyDao->where($proMap)->getField("dbname,fieldname");
				if($datelist){
					foreach ($datelist as $dkey=>$dval){
						$tablename=$dkey."_sub_".$dval;
						$sql="delete  from  `{$tablename}` ";
						$result=$model->query($sql);
						$autokey="ALTER TABLE {$tablename} AUTO_INCREMENT = 1;";
						$autoresult=$model->query($autokey);
						$model->commit();
						if(!$result){
							$msg.=$model->getDbError();
							$endresult=false;
						}
							
					}
				}
				$endsql="delete  from `{$val['tablename']}`   ";
				$result=$model->query($endsql);
				logs('清理数据---'.$endsql,'cleardate');
				$autokey="ALTER TABLE {$val['tablename']} AUTO_INCREMENT = 1;";
				$result=$model->query($autokey);
				logs('重置key---'.$autokey,'cleardate');
				$model->commit();
				if(!$result){
					$msg.=$model->getDbError();
					$endresult=false;
				}
			}
		}
		if($endresult==false){
			$this->error("执行失败！");
		}else{
			$this->success("执行成功！");
		}
	}
	public function createdmcatch(){
		for($i = 1;$i <= 10;$i++){ 
			$randtable="update_last_cache".$i;//通过当前数据id除以分表数量的余数来确定缓存哪张表,被除数分表开关
			$cacheTable=M()->execute("CREATE TABLE if not exists `".$randtable."` (
					  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
					  `backupdata` varchar(10240) DEFAULT NULL COMMENT '修改前记录',
				      `tablename` varchar(100) DEFAULT NULL COMMENT '表名',
					  `tableid` int(11) DEFAULT NULL COMMENT '对应ID',
				      `sqltype` varchar(10) DEFAULT NULL COMMENT 'CURD类型',
					  `randnum` int(4) DEFAULT NULL COMMENT '随机数',
					  `createid` int(10) DEFAULT NULL COMMENT '创建人',
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='漫游累加累减缓存表';");
			
		}
		$this->success("执行成功！");
	}
	
}