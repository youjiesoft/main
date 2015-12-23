<?php
/**
 * @Title: MisOaArchivesManageAction 
 * @Package package_name
 * @Description: todo(档案管理) 
 * @author xiafengqin 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-9-23 下午6:42:11 
 * @version V1.0
 */
class MisOaArchivesManageMasAction extends CommonAction{
	function _filter(&$map){
		//查找数据的条件
		$year=$this->escapeChar($_REQUEST['year']);
		$typeid=$this->escapeChar($_REQUEST['typeid']);
		$fondsnum=$this->escapeChar($_REQUEST['fondsnum']);
		$this->assign("year",$year);
		if($year ){
			$yearList =array_unique(array_filter (explode(",",$this->findAlldept($yearList,$year))));
			$map['year'] = array(' in ',$yearList);
		}
		if($typeid ){
			$typeList =array_unique(array_filter (explode(",",$this->findAlldept($typeList,$typeid))));
			$map['typeid'] = array(' in ',$typeList);
		}
		if($fondsnum ){
			$fondsnumList =array_unique(array_filter (explode(",",$this->findAlldept($fondsnumList,$fondsnum))));
			$map['fondsnum'] = array(' in ',$fondsnumList);
		}
	}
	
	/**
	 * @Title: index 
	 * @Description: todo(index页面的前置函数)   
	 * @author xiafengqin 
	 * @date 2013-9-24 下午5:20:34 
	 * @throws
	 */
	public function _before_index(){
		$moamModel = D("MisOaArchivesManageMas");//档案表
		$yearList = $moamModel->where('status = 1')->DISTINCT(true)->field('year')->select(); //所有不一样的年
		$typeList = $moamModel->where('status = 1')->DISTINCT(true)->field('typenum')->select();
		$newv = array();
		foreach ($yearList as $k=>$v){
			$k1 = $k +1;
			$newvYear=array(
					'id'=> -$k1 ,
					'pId' => 0,
					'title' => '年份',
					'name' => missubstr($v['year'],18,true).'年',
					'url' => "__URL__/index/jump/jump/year/".$v['year'],
					'rel' => "misoaarchivesmanagesub",
					'target'=>'ajax',
					'open'=>true,
			);
			$newv[] = $newvYear;
			foreach ($typeList as $ke=>$va){
				$newvType=array(
						'id'=>$k1.$ke,
						'pId' => -$k1,
						'title' => '类别号',
						'name' => $va['typenum'],
						'url' => "__URL__/index/jump/jump/year/".$v['year']."/typenum/".$va['typenum'],
						'rel' => "misoaarchivesmanagesub",
						'target'=>'ajax',
						'open'=>true,
				);
				$map=array();
				$map['year'] = $v['year'];
				$map['typenum'] = $va['typenum'];
				$fondsnumList = $moamModel->where($map)->DISTINCT(true)->field('fondsnum')->select();
				if ($fondsnumList) {
					$newv[] =$newvType;
				}
				foreach ($fondsnumList as $key=>$val) {
					$newvFondsnum=array(
							'id'=>$k1.$ke.$key,
							'pId' => $k1.$ke,
							'title' => '全宗号',
							'name' => $val['fondsnum'],
							'url' => "__URL__/index/jump/jump/year/".$v['year']."/typenum/".$va['typenum']."/fondsnum/".$val['fondsnum'],
							'rel' => "misoaarchivesmanagesub",
							'target'=>'ajax',
							'open'=>true,
					);
					$newv[] =$newvFondsnum;
				}
			}
		}
		$this->assign('year',$_REQUEST['year']);
		$this->assign('typenum',$_REQUEST['typenum']);
		$this->assign('fondsnum',$_REQUEST['fondsnum']);
		$this->assign('tree',json_encode($newv));
	}
	/**
	 * @Title: _before_add 
	 * @Description: todo(add前置函数)   
	 * @author xiafengqin 
	 * @date 2013-9-25 上午9:09:55 
	 * @throws
	 */
	public function _before_add(){
		//自动生成单号
		$scnmodel = D('SystemConfigNumber');
		$orderno = $scnmodel->GetRulesNO('mis_oa_archives_manage_mas');
		$this->assign("orderno", $orderno);
		//订单号是否可写
		$writable= $scnmodel->GetWritable('mis_oa_archives_manage_mas');
		$this->assign("writable",$writable);
		//部门树
		$model=M("mis_system_department");
		$list =$model->where("status=1")->select();
		$deptlist=$this->selectTree($list,0,0,$deptid);
		$this->assign("deptidlist",$deptlist);
		//获取当天时间
		$curday = date("Y-m-d", time());
		$this->assign("curday", $curday);
		//创建时间
		$this->assign('createtime', time());
		//样本路径
		$url = UPLOAD_SampleArchives .'/卷内文书目录.xls';
		$this->assign('filename','卷内文书目录.xls');
		$this->assign('url' ,$url);
		//判断卷内文书是否有文件
	}
	/**
	 * @Title: _before_insert 
	 * @Description: todo(insert前置函数)   
	 * @author xiafengqin 
	 * @date 2013-9-25 上午10:16:06 
	 * @throws
	 */
	public function _before_insert() {
		$this->checkifexistcodeororder('mis_oa_archives_manage_mas','orderno');
	}
	/**
	 * @Title: _after_insert 
	 * @Description: todo(新增之后插入附件) 
	 * @param unknown_type $id  
	 * @author xiafengqin 
	 * @date 2013-9-25 上午10:28:44 
	 * @throws
	 */
	public function _after_insert($id){
		//插入附件
		if ($id) {
			$this->swf_upload($id,86);
		}
		$mModel = D('MisOaArchivesManageSub');
		$title=$_POST['title'];
		foreach ($title as $key=>$val) {
			if (!$val){
				continue;
			}
			$dateSub = array();
			$dateSub['masid'] = $id;
			$dateSub['deptid'] = $_POST['deptid'][$key];
			$dateSub['referencenum'] = $_POST['referencenum'][$key];
			$dateSub['title'] = $_POST['title'][$key];
			$dateSub['datetime'] = strtotime($_POST['datetime'][$key]);
			$dateSub['page'] = $_POST['page'][$key];
			$dateSub['remark'] = $_POST['remark'][$key];
			$dateSub['createid'] = $_SESSION[C('USER_AUTH_KEY')];
			$dateSub['createtime'] = time();
			$result=$mModel->add ($dateSub);//保存当前数据对象
			if (!$result){
				$this->error ( L('_ERROR_') );
			}
		}
		//导入文件
		if ($_POST['swf_upload_save_name']) {
			$file_path = UPLOAD_PATH_TEMP.$_POST['swf_upload_save_name'];
			$pathinfo = pathinfo($file_path);
			$sheetData = array();
			$modelDbFields = $mModel->getDbFields();
			unset($modelDbFields[0]);
			$sql = " INSERT INTO mis_oa_archives_manage_sub (";
			foreach ($modelDbFields as $key => $val){
				$sql .= ($key == 1 ? "`" .$val. "`" : ",`" .$val. "`");
			}
			$sql .= ") values " ;
			if($pathinfo['extension'] == "xls"){
				$this->importNewFileExcel5($file_path, $mModel, $sql, $modelDbFields, $id);
			} else if ($pathinfo['extension'] == "xlsx") {
				$this->error('文件格式不正确,请上传.xls 格式文件');
			} else {
				$this->error('文件格式不正确');
			}
		}
	}
	private function importNewFileExcel5($file_path, $model, $sql, $modelDbFields, $masid){
		import('@.ORG.PHPExcel.excel_reader2', '', $ext='.php');
		//创建对象
		$data = new Spreadsheet_Excel_Reader();
		//设置文本输出编码
		$data->setOutputEncoding('UTF-8');
		//读取Excel文件
		$data->read($file_path);
		$sheetData = $data->sheets;
		C ( "TOKEN_ON", false );//关闭表单验证
		if (!$sheetData[0]['cells']) {
			$this->error("文件没有数据，请核查");
		}
		$i = 1;//组织sql语句
		$sqleacl = $sql;
		foreach ($sheetData[0]['cells'] as $key => $val) {
			if ($key == 1) {
				continue;
			}
			if ($i == 1) {
				$values =  " (";
			}else {
				$values =  " ,(";
			}
			$i = false;
			$option = "";
			foreach ($modelDbFields as $k => $v) {
				switch ($v) {
					case 'masid':
						$option .= "'".$masid."'";
						break;
					case 'datetime':
						$option .= ",'".strtotime(str_replace('.','-',$val[$k])). "'";
						break;
					case 'createtime':
						$option .= ",'".time(). "'";
						break;
					case 'status' :
						$option .=",'1'";
						break;
					case 'createid':
						$option .= ",'".$_SESSION[C('USER_AUTH_KEY')]."'";
						break;
					default:
						$option .= ",'".$val[$k]."'";
						break;
				}
			}
			$values .= $option . ")";
			$sql .= $values;
			if ($key % 1000  == 0) {
				$result = $model->execute($sql);
				if ($result === false) {
					$this->error ( L('_ERROR_') );
				}
				$i = 1;
				$sql = $sqleacl;
			}
		}
		$result = $model->execute($sql);
		if ($result === false) {
			$this->error ( L('_ERROR_') );
		}
		C ( "TOKEN_ON", true );
		$this->success ( L('_SUCCESS_') );
	}
	
	
	/**
	 * @Title: _before_edit 
	 * @Description: todo(edit前置函数)   
	 * @author xiafengqin 
	 * @date 2013-9-25 上午10:30:32 
	 * @throws
	 */
	public function _before_edit(){
		//获取附件信息
		$this->getAttachedRecordList($_REQUEST['id']);
		//编号可写
		$model1	=D('SystemConfigNumber');
		$writable= $model1->GetWritable('mis_oa_archives_manage_mas');
		$this->assign("writable",$writable);
		//当前单的明细
		$model = D('MisOaArchivesManageSub');
		$map['masid'] = $_REQUEST['id'];
		$map['status'] = 1;
		$sublist = $model->where($map)->select();
		$this->assign("sublist",$sublist);
		//样本路径
		$url = UPLOAD_SampleArchives .'/卷内文书目录.xls';
		$this->assign('filename','卷内文书目录.xls');
		$this->assign('url' ,$url);
	}
	/**
	 * @Title: editview 
	 * @Description: todo(不可修改的页面)   
	 * @author xiafengqin 
	 * @date 2013-9-26 下午2:01:46 
	 * @throws
	 */
	public function view(){
		//获取附件信息
		$this->getAttachedRecordList($_REQUEST['id']);
		//编号可写
		$model1	=D('SystemConfigNumber');
		$writable= $model1->GetWritable('mis_oa_archives_manage_mas');
		$this->assign("writable",$writable);
		//当前单的明细
		$model = D('MisOaArchivesManageSub');
		$map['masid'] = $_REQUEST['id'];
		$map['status'] = 1;
		$sublist = $model->where($map)->select();
		$this->assign("sublist",$sublist);
		
		$name='MisOaArchivesManageMas';
		$model = D ( $name );
		$id = $_REQUEST [$model->getPk ()];
		$map = array();
		$map['id']=$id;
		if ($_SESSION["a"] != 1) $map['status'] = 1;
		$vo = $model->where($map)->find();
		if(empty($vo)){
			$this->display ("Public:404");
			exit;
		}
		//读取动态配制
		$this->getSystemConfigDetail($name);
		//扩展工具栏操作
		$scdmodel = D('SystemConfigDetail');
		$toolbarextension = $scdmodel->getSubDetail($modelname,true,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		// 上一条数据ID
		$map['id'] = array("lt",$id);
		$updataid = $model->where($map)->order('id desc')->getField('id');
		$this->assign("updataid",$updataid);
		// 下一条数据ID
		$map['id'] = array("gt",$id);
		$downdataid = $model->where($map)->getField('id');
		$this->assign("downdataid",$downdataid);
		$this->assign( 'vo', $vo );
		$this->display ();
		
	}
	public function _before_update() {
		$this->checkifexistcodeororder('mis_oa_archives_manage_mas','orderno',1);
	}
	public function _after_update($id) {
		$masid = $_REQUEST['id'];
		//保存附件
		if ($id){
			$this->swf_upload($_REQUEST['id'],86);
		}
		//保存
		$mModel = D('MisOaArchivesManageSub');
		$edittitle=$_POST['edittitle'];
		foreach ($edittitle as $key=>$val){
			if (!$val){
				continue;
			}
			$_POST['id'] = $key;
			$_POST['deptid'] = $_POST['editdeptid'][$key];
			$_POST['referencenum'] = $_POST['editreferencenum'][$key];
			$_POST['title'] = $_POST['edittitle'][$key];
			$_POST['datetime'] = strtotime($_POST['ecitdatetime'][$key]);
			$_POST['page'] = $_POST['editpage'][$key];
			$_POST['remark'] = $_POST['editremark'][$key];
			if (false === $mModel->create ()) {
				$this->error ( $mModel->getError () );
			}
			//保存当前数据对象
			$list=$mModel->save ();
			if (!$list){
				$this->error ( L('_ERROR_') );
			}
		}
		// 新增
		unset($_POST['id']);
		foreach ($_POST['addtitle'] as $key=>$val){
			if (!$val){
				continue;
			}
			$dateSub = array();
			$dateSub['masid'] = $masid;
			$dateSub['deptid'] = $_POST['adddeptid'][$key];
			$dateSub['referencenum'] = $_POST['addreferencenum'][$key];
			$dateSub['title'] = $_POST['addtitle'][$key];//数量
			$dateSub['datetime'] = strtotime($_POST['adddatetime'][$key]);
			$dateSub['page'] = $_POST['addpage'][$key];
			$dateSub['remark'] = $_POST['addremark'][$key];
			$dateSub['createid'] = $_SESSION[C('USER_AUTH_KEY')];
			$dateSub['createtime'] = time();
			$result=$mModel->add ($dateSub);//保存当前数据对象
			if (!$result ) {
				$this->error ( L('_ERROR_') );
			}
		}
		//导入
		if ($_POST['swf_upload_save_name']) {
			$file_path = UPLOAD_PATH_TEMP.$_POST['swf_upload_save_name'];
			$pathinfo = pathinfo($file_path);
			$sheetData = array();
			$modelDbFields = $mModel->getDbFields();
			unset($modelDbFields[0]);
			$sql = " INSERT INTO mis_oa_archives_manage_sub (";
			foreach ($modelDbFields as $key => $val){
				$sql .= ($key == 1 ? "`" .$val. "`" : ",`" .$val. "`");
			}
			$sql .= ") values " ;
			if($pathinfo['extension'] == "xls"){
				$this->importNewFileExcel5($file_path, $mModel, $sql, $modelDbFields, $masid);
			} else if ($pathinfo['extension'] == "xlsx") {
				$this->error('文件格式不正确,请上传.xls 格式文件');
				//$this->importNewFileExcel2007($file_path, $model, $sql, $modelDbFields);
			} else {
				$this->error('文件格式不正确');
			}
		}
	}
	/**
	 * @Title: subdelete 
	 * @Description: todo(删除明细)   
	 * @author xiafengqin 
	 * @date 2013-10-21 下午5:12:54 
	 * @throws
	 */
	public function subdelete(){
		$id = $_POST['id'];// 明细ID
		$model = D("MisWorkFacilityApplySub");
		$map['id'] = $id;
		$res = $model->where($map)->delete();
		$model->commit();
		echo $res;
	}
}
