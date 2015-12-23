<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(导入顺序图明细控制器) 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-6-1 上午11:51:15 
 * @version V1.0
 */
class MisImportExcelSubAction extends CommonAction {
	/**
	 * @Title: _before_add 
	 * @Description: todo(新增之前操作数据)   
	 * @author liminggang 
	 * @date 2013-6-1 上午11:51:40 
	 * @throws
	 */
	 public function _before_add(){
		  $model=D("MisImportExcel");
		  $eid=$this->escapeStr($_GET['eid']);
		  $list=$model->find($eid);
		  if( !$list['tableobj'] ){
			   $list=$model->find($list['inheritid']);
		  }
		  $model=M();
		  $model1=D("MisImportExcelSub");
		  $already_columns = $model1->where("eid=".$eid)->getField("id,fieldname");
		  $columns = $model->query("show columns from ".$list['tableobj']);
		  $model2=M("INFORMATION_SCHEMA.COLUMNS","","",1);
		  $columnstitle = $model2->where("table_name = '".$list['tableobj']."' AND TABLE_SCHEMA = '".C('DB_NAME')."'")->getField("COLUMN_NAME,COLUMN_COMMENT");

		  /*SELECT COLUMN_NAME, COLUMN_COMMENT
		  FROM INFORMATION_SCHEMA.COLUMNS
		  WHERE table_name = 'mis_purchase_suppliertype'
		  AND TABLE_SCHEMA = 'exnt'
		  LIMIT 0 , 30*/
		  foreach($columns as $k=>$v){
			   if( in_array($v['Field'],$already_columns) ){
				    unset($columns[$k]);
				    continue;
			   }
			   if($v['Extra']=='auto_increment') unset($columns[$k]);
		  }

		  $this->assign("columns", $columns);
		  $this->assign("columnstitle", $columnstitle);
		  $this->assign("eid", $list['id']);
		  //查询已保存字段
		  $model=D("MisImportExcelSub");
		  $list2=$model->where("eid=".$list['id'])->getField("id,fieldname");
		  $this->assign("alreadycols", $list2);
	 }
	 /**
	  * (non-PHPdoc)
	  * @Description: todo(插入方法)   
	  * @see CommonAction::insert()
	  */
	 public function insert(){
		  $data=array();
		  $cols=array();
		  $cols1=$_POST['cols'];
		  $cols2=$_POST['colsmust'];
		  if($cols2){
			   foreach($cols2 as $k=>$v){
				    array_push($cols,$v);
			   }
		  }
		  if($cols1){
			   foreach($cols1 as $k=>$v){
				    array_push($cols,$v);
			   }
		  }
		  $eid = intval($_POST['eid']);
		  $i=0;
		  $model=D("MisImportExcelSub");
		  foreach($cols as $k=>$v){
			   $data[$i]['eid']=$eid;
			   $data[$i]['name']=$_POST['columnstitle'][$v];
			   $data[$i]['ifmust']=$_POST['ifmust'][$v];
			   $data[$i]['fieldname']=$v;
			   if(C('TOKEN_NAME')) $data[$i][C('TOKEN_NAME')]= $_POST[C('TOKEN_NAME')];
			   if (false === $model->create ($data[$i])) {
				   $this->error ( $model->getError () );
			   }
			   $i++;
		  }
		  $list = $model->addall($data);
		  if ($list!==false) {//保存成功
			  $this->success ( L('_SUCCESS_') ,'',$list);
		  } else {
			  $this->error ( L('_ERROR_') );
		  }
	 }
	 /**
	  * (non-PHPdoc)
	  * @Description: todo(修改方法)   
	  * @see CommonAction::update()
	  */
	 public function update(){
		  $data=array();
		  $cols=$_POST['columnstitle'];
		  $ifcheckexist=$_POST['columnscheckexist'];
		  $ystable=$_POST['subimporttableobj'];
		  $ysfield=$_POST['subimporttablefieldobj'];
		  $funcname=$_POST['funcname'];
		  $optionsval=$_POST['optionsval'];
		  $checkfunc=$_POST['checkfunc'];
		  $model=D("MisImportExcelSub");
		  $i=0;
		  foreach($cols as $k=>$v){
			   $data[$i]['id']=$k;
			   $data[$i]['name']=$this->escapeStr($v);
			   $data[$i]['ifcheckexist']=0;
			   $data[$i]['bindtable']=$data[$i]['bindfield']=$data[$i]['bindfunc']="";
			   if( $ifcheckexist[$k] ) $data[$i]['ifcheckexist']=1;
			   if( $ystable[$k] ) 	$data[$i]['bindtable']=$ystable[$k];
			   if( $ysfield[$k] ) 	$data[$i]['bindfield']=$ysfield[$k];
			   if( $funcname[$k] ) 	$data[$i]['bindfunc']=$funcname[$k];
			   if( $checkfunc[$k] ) $data[$i]['checkfunc']=$checkfunc[$k];
			   if( $optionsval[$k] ) $data[$i]['optionsval']=$optionsval[$k];
			   

			   if(C('TOKEN_NAME')) $data[$i][C('TOKEN_NAME')]= $_POST[C('TOKEN_NAME')];
			   if (false === $model->create ($data[$i])) {
				   $this->error ( $model->getError () );
			   }
			   $list = $model->save($data[$i]);
			   if ($list!==false) {//保存成功
				    //$this->success ( L('_SUCCESS_') ,'',$list);
			   } else {
				    $this->error ( L('_ERROR_') );
			   }
			   $i++;
		  }
		  $this->success ( L('_SUCCESS_'));
	 }
	 /**
	  * (non-PHPdoc)
	  * @Description: todo(编辑方法)   
	  * @see CommonAction::edit()
	  */
	 public function edit(){
		  $ids=$this->escapeChar($_GET['id']);
		  $map["id"] = array ('in', explode ( ',', $ids ) );
		  $model=D("MisImportExcelSub");
		  $list2=$model->where( $map )->select();
		  foreach($list2 as $k=>$v ){
			   if($v['bindtable']){
				  $list2[$k]['fieldarr']=$this->comboxgetTableField($v['bindtable']);
			   }
		  }
		  $this->assign("alreadycols", $list2);

		  $model3=M("INFORMATION_SCHEMA.TABLES","","",1);
		  $tables = $model3->where("TABLE_SCHEMA = '".C('DB_NAME')."'")->getField("TABLE_NAME,TABLE_COMMENT");
		  foreach($tables as $k=>$v){
			   if($v==""){
				    $tables[$k]=$k;
			   }else{
				    $v=explode("; InnoDB",$v);
				    $tables[$k]=$v[0];
				    if( count(explode("InnoDB",$v[0])) >1 ){
					     $tables[$k]=$k;
				    }
			   }
		  }
		  $this->assign("tables", $tables);
		  
		  //获取表列信息
		  foreach ($list2 as $key => $val) {
			//如果表有值且表列有值 ，查出这列的备注
			if ($val['bindfield'] && $val['bindtable']) {
				$model2=M("INFORMATION_SCHEMA.COLUMNS","","",1);
				$columnstitle = $model2->where("table_name = '".$val['bindtable']."' AND COLUMN_NAME='".$val['bindfield']."' AND TABLE_SCHEMA = '".C('DB_NAME')."'")->field("COLUMN_NAME,COLUMN_COMMENT")->find();
				//如果该列备注为”” 或NULL,则赋值为该列名
				if ($columnstitle && (!$columnstitle['COLUMN_COMMENT'])) {
					$columnstitle['COLUMN_COMMENT'] = $columnstitle['COLUMN_NAME'];
				}
				$this->assign('columnstitle',$columnstitle);
			}
		  }
		  $this->display();
	 }
	 /**
	  * @Title: setsort 
	  * @Description: todo(这里用一句话描述这个方法的作用)   
	  * @author liminggang 
	  * @date 2013-6-1 上午11:52:32 
	  * @throws
	  */
	 public function setsort(){
		  /*
		  $direct=$this->escapeStr($_GET['d']);
		  $id = $this->escapeStr($_GET ['id']);
		  $model = D("MisImportExcelSub");
		  $map['id']=$id;
		  $list=$model->where ( $map )->find();
		  $map1['pid']=$list['pid'];
		  $list1=$model->where ( $map1 )->field('sort,pid,id')->order('sort asc')->select();
		  $count = count($list1);
		  if( $count >1 ){
			   if( $direct=="up"){
				   foreach($list1 as $k=>$v){
					   if($list['sort']==$v['sort']) break;
					   $pos_before=$v['sort'];
					   $id_before =$v['id'];
				   }
				   $model->where ( 'id='.$id_before )->setField( 'sort', $list['sort']);
				   $model->where ( 'id='.$list['id'])->setField( 'sort',$pos_before );
				   $this->success ( L ( '_SUCCESS_' ) );
			   }else if($direct=="down"){
				   $arr=array();
				   $i=1;
				   foreach($list1 as $k=>$v){
					   if( $i<=$count){
						   $pos_next=$list1[$k+1]['sort'];
						   $id_next =$list1[$k+1]['id'];
					   }
					   if($list['sort']==$v['sort']) break
					   $i++;
				   }
				   $model->where ( 'id='.$id_next )->setField( 'sort', $list['sort']);
				   $model->where ( 'id='.$list['id'])->setField( 'sort',$pos_next );
				   $this->success ( L ( '_SUCCESS_' ) );
			   }
		  }*/
	 }
	 /**
	  * @Title: comboxgetTableField 
	  * @Description: todo(页面JS请求，返回数据) 
	  * @param unknown_type $t
	  * @return multitype:multitype:string    
	  * @author liminggang 
	  * @date 2013-6-1 上午11:55:41 
	  * @throws
	  */
	 public function comboxgetTableField( $t='' ){
		  $model=M();
		  if( $t=="" ){
			   $table = $this->escapeStr($_POST['table']);
		  }else{
			   $table = $t;
		  }
		  $arr=array(array("","请选择映射对象字段"));
		  if ($table!='') {
			   $columns = $model->query("show columns from ".$table);
			   $model2=M("INFORMATION_SCHEMA.COLUMNS","","",1);
			   $columnstitle = $model2->where("table_name = '".$table."' AND TABLE_SCHEMA = '".C('DB_NAME')."'")->getField("COLUMN_NAME,COLUMN_COMMENT");
			   foreach($columns as $k=>$v){
				    $title=$v['Field'];
				    if( $columnstitle[$v['Field']] ) $title=$columnstitle[$v['Field']];
				    array_push($arr,array($v['Field'], $title));
			   }
		  }
		  if( $t=="" ){
			  echo json_encode($arr);
		  }else{
			   return $arr;
		  }

	 }
}
?>