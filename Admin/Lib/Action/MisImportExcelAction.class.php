<?php
/**
 * @Title: file_name
 * @Package package_name
 * @Description: todo(导入顺序图控制器)
 * @author liminggang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-6-1 上午11:04:22
 * @version V1.0
 */
class MisImportExcelAction extends CommonAction {
	/**
	 * @Title: _filter
	 * @Description: todo(输出模板条件过滤器方法)
	 * @param unknown_type $map
	 * @author liminggang
	 * @date 2013-6-1 上午11:04:41
	 * @throws
	 */
	public function _filter(&$map){
		$map['pid']	=0;
		if( intval($_REQUEST['pid']) >0 ){
			$map['pid'] = intval($_REQUEST['pid']);
		}
		$_SESSION['currentNodeId']=$map['pid'];
	}
	/**
	 * (non-PHPdoc)
	 * @Description: todo(输出首页模板方法)
	 * @see CommonAction::index()
	 */
	public function index(){
		$map['status']=array("gt",0);
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$model=D("MisImportExcel");
		$list = $model->where($map)->select();
		$this->assign("list", $list);
		if (!$preptitle) $preptitle="导入顺序图";
		$this->assign("preptitle", $preptitle);
		$prepid=$model->where('status=1 and id='.$map['pid'])->getField('pid');
		$this->assign('prepid',$prepid);
		$this->assign('pid',$map['pid']);
		$this->display();
	}
	/**
	 * @Title: orderimage
	 * @Description: todo(导入顺序图输出模板)
	 * @author liminggang
	 * @date 2013-6-1 上午11:11:20
	 * @throws
	 */
	public function orderimage(){
		$id= intval($_REQUEST['id']) ? intval($_REQUEST['id']):0;
		$this->assign('id',$id);
		$this->display();
	}
	/**
	 * @Title: lookuporderimage2
	 * @Description: todo(JS页面自动跳转的URL输出模板页面)
	 * @author liminggang
	 * @date 2013-6-1 上午11:14:36
	 * @throws
	 */
	public function lookuporderimage2(){
		$model=D("MisImportExcel");
		$map['id'] = intval($_REQUEST['id']) ? intval($_REQUEST['id']):0;
		$map['status']=1;
		$list = $model->where($map)->field("id,name")->select();
		$list=$this->getTree($list);
		$this->assign('id',$map['id']);
		$this->assign("jsontree", json_encode($list[0]));
		$this->display();
	}
	/**
	 * @Title: _before_add
	 * @Description: todo(新增方法之前操作特殊数据)
	 * @author liminggang
	 * @date 2013-6-1 上午11:05:34
	 * @throws
	 */
	public function _before_add(){
		$pid=$_SESSION['currentNodeId'];
		$pid=$_REQUEST['pid'];
		if($pid){
			$this->assign("pid", $pid);
		}
		$model=M();
		$model2=D("MisImportExcel");
		$alreadytable = $model2->where("tableobj!=''")->getField("id,tableobj");
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

			if(in_array($k,$alreadytable)){
				unset($tables[$k]);
			}
		}
		$this->assign("tables", $tables);
		$this->assign("alreadytables", $alreadytable);
	}
	/**
	 * @Title: _before_update
	 * @Description: todo(修改保存前置函数)
	 * @author liminggang
	 * @date 2013-6-1 上午11:05:50
	 * @throws
	 */
	public function _before_update(){
		$linktable = $_POST['linktable'];
		$linkfield = $_POST['linkfield'];
		$thefield = $_POST['thefield'];
		$linktables = array();
		$n = count($linktable);
		foreach ($linktable as $k => $v) {
			if ($v) {
				$table = $v;
				$field = '';
				$field2 = '';
				if ($linkfield[$k]) {
					$field = $linkfield[$k];
				} else {
					$this->error("请选择关联字段！");
					exit;
				}
				if ($thefield[$k]) {
					$field2 = $thefield[$k];
				} else {
					$this->error("请选择本表字段！");
					exit;
				}
				$linktables[] = array(
						'linktable' => $table,
						'linkfield' => $field,
						'thefield' => $field2
				);
			} else {
				continue;
			}
		}
		if ($linktables) {
			$_POST['linktables'] = serialize($linktables);
		} else {
			$_POST['linktables'] = "";
		}
	}
	/**
	 * (non-PHPdoc)
	 * @Description: todo(编辑方法)
	 * @see CommonAction::edit()
	 */
	public function edit(){
		$model1=M("INFORMATION_SCHEMA.TABLES",'','',1);
		$tabletitle = $model1->where("TABLE_SCHEMA = '".C('DB_NAME')."'")->getField("TABLE_NAME,TABLE_COMMENT");
		foreach($tabletitle as $k=>$v){
			if($v==""){
				$tabletitle[$k]=$k;
			}else{
				$v=explode("; InnoDB",$v);
				$tabletitle[$k]=$v[0];
				if( count(explode("InnoDB",$v[0])) >1 ){
					$tabletitle[$k]=$k;
				}
			}
		}
		$this->assign("tables", $tabletitle);
		$model2=D('MisImportExcelSub');
		$model3=D('MisImportExcel');
		$id=$this->escapeStr($_GET['id']);
		$map['eid']=$id;
		$vo = $model3->where("id=".$id)->find();
		$linktables = array();
		$num = 0;
		if ($vo['linktables']) {
			$linktables = unserialize($vo['linktables']);
			$model5=M("INFORMATION_SCHEMA.COLUMNS","","",1);
			$num = count($linktables);
			foreach ($linktables as $k => $v) {
				$columnstitle = $model5->where("table_name = '".$v['linktable']."' AND TABLE_SCHEMA = '".C('DB_NAME')."'")->getField("COLUMN_NAME,COLUMN_COMMENT");;
				$linktables[$k]['linkfields'] = $columnstitle;
			}
		}
		$vo['linktables'] = $linktables;
		if($vo['inheritid']){
			$map['eid']=$vo['inheritid'];
		}
		$map['status']=array("egt",0);
		$this->assign("vo",$vo);
		$this->assign("num",$num);
		$count =$model2->where($map)->order('sort')->count();
		$pagenum=$_REQUEST['pageNum']?$_REQUEST['pageNum']:1;
		$numPerPage=10;
		$sublist =$model2->where($map)->order('sort')->limit(($pagenum-1)*$numPerPage.','.$numPerPage)->select();
		$this->assign ( 'totalCount', $count );
		$this->assign ( 'numPerPage', $numPerPage);
		$this->assign ( 'dwznumPerPage', C('PAGE_DWZLISTROWS'));
		$this->assign ( 'currentPage', !empty($_REQUEST[C('VAR_PAGE')])?$_REQUEST[C('VAR_PAGE')]:1);
		
 		$url = $this->orderSortMapToStr($map);
		$this->assign('url', $url);
		$this->assign('map', ($sql));
		$this->assign("sublist", $sublist);
		$this->display();
	}
	/**
	 * @Title: orderSortMapToStr
	 * @Description: todo(把查询条件转换成url可用的数据，如“eid:19-status:egt:0”  解释：字段：值 (字段:符号:值)，用“-”分开)
	 * @param array $where 条件值(数组形式)
	 * @author jiangx
	 * @date 2013-8-6
	 * @throws
	 */
	private function orderSortMapToStr($where){
		$sqlstr = "";
		foreach ($where as $key => $val) {
			if (is_array($val)) {
				if (is_array($val[1])) {
					$sqlstr .= $key .':'. $val[0] .':'. implode(',', $val[1]).'-';
				}else{
					$sqlstr .= $key .':'. $val[0] .':'. $val[1].'-';
				}
			} else {
				$sqlstr .= $key .':'. $val.'-';
			}
		}
		return $sqlstr;
	}

	/**
	 * (non-PHPdoc)
	 * @Description: todo(重写了父类的生成树的方法)
	 * @see CommonAction::getTree()
	 */
	public function getTree( $list ){
		$model=D("MisImportExcel");
		foreach($list as $k=>$v){
			$list[$k]['data']=array();
			$list2 = $model->where("status=1 and pid=".$v['id'])->field("id,name")->select();
			if( count($list2)>0 ){
				$list[$k]['children']= $this->getTree($list2);
			}
		}
		return $list;
	}

	/**
	 * @Title: importexceladd
	 * @Description: todo(下载实例导入新增)
	 * @author liminggang
	 * @date 2013-6-1 上午11:41:13
	 * @throws
	 */
	public function misimportexceladd(){
		$model=D("MisImportExcel");
		$this->assign('model',$_REQUEST['model']);
		$this->assign('importcatalog',1);
		if( ! empty($_REQUEST["datatable"])){
			$this->assign('tableID',$_REQUEST['tableID']);
			$this->assign('datatable',$_REQUEST["datatable"]);
		}
		if( $this->escapeStr($_GET['download']) >0 ){
			$timestamp = time();
			$name = $_REQUEST["model"];
			$filename = getFieldBy($name, 'name', 'title', 'node');
			$scdmodel = D('SystemConfigDetail');
			if(empty($_REQUEST["datatable"])){
				$detailList = $scdmodel->importDataGetDetail ($name,1);
			}else{
				$detailList = $scdmodel->importDataGetDetail ($name,$_REQUEST["datatable"]);
			}
			$filename = $filename.".xls";
			ob_end_clean();
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $timestamp + 86400) . ' GMT');
			header('Expires: ' . gmdate('D, d M Y H:i:s', $timestamp + 86400) . ' GMT');
			header('Cache-control: max-age=86400');
			header('Content-Encoding: utf-8');
			header("Content-Disposition: attachment; filename=\"{$filename}\"");
			header("Content-type: application/vnd.ms-excel");
			header("Content-Transfer-Encoding: binary");
			
			$_exportData=array();
			foreach($detailList as $k=>$v){
				if($v['required']) $v['showname']="*".$v['showname'];
				if( ! empty($v["unfunc"][0][0]) && $v["unfunc"][0][0]=="unitExchange"){
					$unitStr = unitInfo($v["unfuncdata"][0][0][2]);
					$v['showname'].="（".$unitStr["danweimingchen"]."）";
				}
				$_exportData[]=$v['showname'];
			}
			import('@.ORG.PHPExcel.IOFactory', '', $ext='.php');
			$objPHPExcel = new PHPExcel();
			 
			//设置表头
			foreach($_exportData as $k => $v){
				$colum = PHPExcel_Cell::stringFromColumnIndex($k);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum.'1', $v);
			}
// 			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
// 			$objPHPExcel->setActiveSheetIndex(0);
// 			$objActSheet = $objPHPExcel->getActiveSheet();
// 			$objValidation = $objActSheet->getCell("A2")->getDataValidation(); //这一句为要设置数据有效性的单元格
// 			$objValidation -> setType(PHPExcel_Cell_DataValidation::TYPE_LIST)
// 			->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)
// 			->setAllowBlank(false)
// 			->setShowInputMessage(true)
// 			->setShowErrorMessage(true)
// 			->setShowDropDown(true)
// 			->setErrorTitle('输入的值有误')
// 			->setError('您输入的值不在下拉框列表内.')
// 			->setPromptTitle('设备类型')
// 			->setFormula1('"2014,2015,2016"');
// 			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', "2014");
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");
			$objWriter->save('php://output');
		}
		$this->display();
	}
	/**
	 * @Title: importexcelinsert
	 * @Description: todo(导入数据内容)
	 * @author liminggang
	 * @date 2013-6-1 上午11:16:49
	 * @throws
	 */
	public function misimportexcelinsert(){
		$this_model=$_POST['model'];
		$datatablemodel=$_POST['datatable'];
		$model=D("MisImportExcel");
		$model2=D("MisImportExcelSub");
		import ( '@.ORG.UploadFile');
		$step = $_POST['step'];
		$tableID = $_POST['tableID'];
		if($step == 2 ){
			if($_FILES){
				$upload = new UploadFile(); // 实例化上传类
				$upload->savePath = UPLOAD_PATH."/tempImportExcel/";//C('savePath'); // 上传目录
				if ( !file_exists($upload->savePath) ) {
					$this->createFolders($upload->savePath); //判断目标文件夹是否存在
				}
				$upload->saveRule =date("Y_m_d_H_i_s")."_".$this_model;
				$upload->allowExts= array("xls","xlsx");
				if(!$upload->upload()) { // 上传错误提示错误信息
					$this->error($upload->getErrorMsg());
				}else{ // 上传成功 获取上传文件信息
					$info = $upload->getUploadFileInfo();
				}
			}
			$yuan_fileName=(basename($info[0]["name"],".".$info[0]["extension"]));
			$filetype =end(explode(".",$info[0]['savename']));
			if($filetype=="xls" || $filetype=="xlsx"){
				import('@.ORG.PHPExcel.IOFactory', '', $ext='.php');
				$inputFileName =UPLOAD_PATH."/tempImportExcel/".$info[0]['savename'];
				if($filetype=="xls"){
					$inputFileType = 'Excel5';
				}else if( $filetype=="xlsx"){
					$inputFileType = 'Excel2007';
				}
				$objReader = PHPExcel_IOFactory::createReader($inputFileType);
				$objPHPExcel = $objReader->load($inputFileName);
					
				$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
				$sheet_list = $objPHPExcel->getSheetNames();
				$k_sheetData = array();//转"A" => 0
				foreach($sheetData[1] as $k => $v){
					$index = PHPExcel_Cell::columnIndexFromString($k);
					$k_sheetData[$index-1] = $v;
				}
				if(count($sheet_list)>1)
				{
					//$this->ajaxReturn(array("sheet_list"=>$sheet_list));
					//exit;
				}
				$model=D("MisSystemRecursion");

				//  				$list1=$model->dataShow($sheetData,array("key"=>"id","pkey"=>"groupid"),1,0);
				// 				dump($list);
				// 				exit;
				$dataList['title']=$sheetData[1];
				$dataList['data']=$sheetData;
				$cl_list = array();
				$keywords = array();
				$row_show = array();
				$name = $_REQUEST["model"];//$this->getActionName();
				$scdmodel = D('SystemConfigDetail');
				$detailList = $scdmodel->importDataGetDetail ($name,1);
				$ifmustArr = array();
				foreach($detailList as $k => $v)
				{
					$keywords[] = $v["showname"];
					$cl_list["title"][] = array("fieldname"=>$v["name"],"name"=>$v["showname"],"ifmust"=>$v["required"]);
					if($v["required"]){
						$ifmustArr[] = $v["name"];
					}
				}
				foreach($dataList['data'] as $k => $v)
				{
					$is_show = 1;
					foreach($v as $kk => $vv)
					{
						if(in_array(trim($vv),str_replace("*", "", $keywords)))
						{
							$is_show = 0;
							break;
						}
					}
					if($is_show){
						$is_show = 0;
						foreach($v as $kk => $vv)
						{
							if( ! empty($vv))
							{
								$is_show = 1;
								break;
							}
						}
					}
					$row_show[$k] = $is_show;
				}
				$cl_list["data"]=$dataList['data'];
				$cl_list["count"]=count($sheetData[1]);
				//array_shift($dataList['data']);
				$excel=true;
			}
			if(empty($datatablemodel)){
				$ajaxData = array(
						"sheet_list"=>$sheet_list,
						"isDataTable"=>0,
						"keywords"=>$keywords,
						"jd_id"=>$id,
						"yuan_fileName"=>$yuan_fileName,
						"filename"=>$inputFileName,
						"row_show"=>$row_show,
						"yuan_list"=>$dataList,
						"cl_list"=>$cl_list,
						"title"=>$k_sheetData,
						"ifmustArr"=>$ifmustArr,
						"model"=>$this_model
				);
				$this->ajaxReturn($ajaxData);
			}else{
				$data = array();
				$scdmodel = D('SystemConfigDetail');
				$detailList = $scdmodel->importDataGetDetail ($this_model,$datatablemodel);
				$title = array();
				foreach($detailList as $k=>$v){
					$title[]=$v["name"];
				}
				if( ! empty($dataList['data'])){
					foreach($dataList['data'] as $k => $v){
						if($k!=1){
							$temp = array();
							foreach($v as $kk => $vv){
								$temp[$title[PHPExcel_Cell::columnIndexFromString($kk)-1]] = $vv;
							}
							$data[] = $temp;
						}
					}
				}
				foreach($data as $k=>$v){ //转换数据
					foreach($v as $k2=>$v2 ){
						if( ! empty($detailList[$k2]["unfunc"]) && $detailList[$k2]["unfunc"][0][0]!="unitExchange"){ //判断数据是否需要转换
							$unfunc = $detailList[$k2]["unfunc"][0];
							$unfuncdata = $detailList[$k2]["unfuncdata"][0];
							$value = getConfigFunction($v2,$unfunc,$unfuncdata,$v);
							if( ! empty($value) && $value!="error" && $value){
								$data[$k][$k2] = $value;
							}else{
							 	$data[$k][$k2] = "";
							}
						}
					}
				}
				$ajaxData = array(
						"sheet_list"=>$data,
						"isDataTable"=>1,
						"tableID"=>$tableID
				);
				
				$this->ajaxReturn($ajaxData);
			}
		}else{
			$this->display();
		}
	}

	public function misimportexcelinsert_add(){
// 		$id=$this->escapeStr($_POST['jd_id']);
		$data_row_id=$_POST['data_row_id'];
		$yuan_fileName=$_POST['yuan_fileName'];
		$inputFileName=$this->escapeStr($_POST['filename']);
		$import_field = array_unique($_POST['import_field']);
		$dataList = $_POST['dataList'];
		$model = $_POST['model'];
		import('@.ORG.PHPExcel.IOFactory', '', $ext='.php');
		
		$newImportField = array(); //处理后的新导入字段数组
		foreach($import_field as $k => $v){
			if( ! empty($v)){
				$newImportField[PHPExcel_Cell::stringFromColumnIndex($k)] = $v;
			}
		}
		$newKey = array();//需要导入列的表头key    A、B、C.....AA、AB、AC
		foreach($newImportField as $k => $v){
			$newKey[] = $k;
		}
		foreach($dataList as $k => $v)
		{
			foreach($v as $kk => $vv){
				if(!in_array($kk,$newKey)){
					unset($dataList[$k][$kk]);
				}
			}
			if(!in_array($k,$data_row_id))
			{
				unset($dataList[$k]);
			}
		}
		if(count($dataList)>0){
			$this->commonimportAction($list,$dataList,$list1,$excel,$inputFileName,$yuan_fileName,$newImportField,$model);
		}
		else
		{
			$this->error("请至少选择一条数据进行导入");
		}
	}

	public function excellist(){
		$list = $_POST['list'];
		$this->assign('dataList', $list);
		if($_POST['pageNum']){
			$pageNum=$_POST['pageNum'];
		}else{
			$pageNum=1;
		}
		
		//$this->getPager($list["cl_list"]["data"],$pageNum,5);
		$this->display();
	}

	/**
	 * 分页函数
	 * @param array $data 所有数据
	 * @param int $index 当前页
	 * @param int $count 每页显示记录数
	 * @exception
	 * 		传入数据key 值为连续自增
	 */
	function getPager($data , $index=1 , $count=3){

		$allcount = count($data); // 总记录数量
		
		$allPager = $allcount?ceil($allcount / $count):1;	// 总页数
		if($index <= 1){
			// 用户指定的页数最小为1
			$index = 1;
		}
		if($index > $allPager){
			$index = $allPager ; // 指定页最大为总页数
		}

		// 得到开始结束下标
		$start = ($index-1)*$count+1;
		$end = $index * $count;
		$curData=array();
		// 获取当前数据
		for($i =$start ; $i<=count($data)+1;$i++){
			if($i <= $end){
				$curData[$i] = $data[$i];
			}
		}
		$this->assign("totalCount",count($data));
		$this->assign("currentPage",$index);
		$this->assign("numPerPage",intval($allPager));
		$this->assign("list",$curData);
	}
	
	private function oldcommonimportAction($fieldlist,$datalist,$volist,$excel,$filename="",$yuan_fileName=""){
		$table = $volist['tableobj'];
		$errorStr = "";
		if($excel){//如果是xls 或者xlsx 后缀的处理数据
			$title=array();
			foreach($datalist['title'] as $k=>$v){
				$title[]=$v;
			}
			$data1=array();
			foreach($datalist['data'] as $k=>$v){
				$data2=array();
				foreach($v as $k2=>$v2){
					$data2[]=$v2;
				}
				$data1[$k]=$data2;
			}
			$datalist['title']=$title;
			$datalist['data']=$data1;
		}
	
		$field=array();
		$is_pid = false;
		foreach($fieldlist as $k=>$v){
			if($volist["tableobj"]==$v["bindtable"]){
				$is_pid = true;
				break;
			}
		}
		foreach($fieldlist as $k=>$v){
			if($v['ifmust']){
				$fieldlist[$k]['name']="*".$v['name'];
				$v['name']="*".$v['name'];
			}
			if(!in_array($v['name'],$datalist['title'])) $this->error($v['name']."标题列不存在,请与下载例子的模板保持一致");
			if($v['name']!=$datalist['title'][$k]) $this->error("请与下载例子的模板标题列保持一致");
			foreach($datalist['title'] as $k2=>$v2){
				if($v['name']==$v2){
					$v['titlekey']=$k2;
					$field[$k2]=$v;
				}
			}
		}
		$d=$uni=array();
		$i=0;
		$m=M($table);
		$sql = "";
		foreach($datalist['data'] as $k=>$v){
			foreach($v as $k2=>$v2 ){
				if(!isset($field[$k2])) continue;
				$f=$field[$k2];
				$d[$i][$f['fieldname']]=$this->escapeChar($v2);
				$d_title = array();
				foreach($d[0] as $d_k=>$d_v){
					if($d_k!="id")
					{
						$d_title[] = $d_k;
					}
				}
				if( $f['ifmust'] && $d[$i][$f['fieldname']]=="")
				{
					unset($d[$k]);
					$errorStr[]=array("rowId"=>$k,"msg"=>"{columnIndex}".$k."中的数据不能为空","columnIndex"=>$k2+1);//$this->error("第".($k)."行数据中的第".($k2+1)."列数据不能为空");
				}
				if( $f['optionsval']  && $d[$i][$f['fieldname']]!=""){
					$options= explode(";",$f['optionsval']);
					foreach($options as $kop=>$vop){
						$thisval = explode(":",$vop);
						if($thisval[1]==$d[$i][$f['fieldname']]){
							$d[$i][$f['fieldname']] = $thisval[0];
						}
					}
				}
					
				if( $f['bindfunc'] && $d[$i][$f['fieldname']]!=""){
					if( $f['bindfunc']=="strtotime" ){
						if($d[$i][$f['fieldname']]) $d[$i][$f['fieldname']] = strtotime($d[$i][$f['fieldname']]);
					}
				}
				if( $f['checkfunc'] && $d[$i][$f['fieldname']]!=""){
					$alertmsg = $this->regex($d[$i][$f['fieldname']],$f['checkfunc']);
					if( $alertmsg!="" ){
						unset($d[$k]);
						$errorStr[]=array("rowId"=>$k,"msg"=>"{columnIndex}".$k.$alertmsg,"columnIndex"=>$k2);//$this->error("第".($k)."行数据中的第".($k2+1)."列".$alertmsg);
					}
				}
					
				if( $f['bindtable'] && $f['bindfield'] && $d[$i][$f['fieldname']]!=""){
					$map2=array();
					$map2[$f['bindfield']]=strval($d[$i][$f['fieldname']]);
					$mbind=M($f['bindtable']);
					$getfieldid=$mbind->where($map2)->getField("id");
					if( !$getfieldid )
					{
						unset($d[$k]);
						$errorStr[]=array("rowId"=>$k,"msg"=>"{columnIndex}".$k."中的数据不存在","columnIndex"=>$k2);//$this->error("第".($k)."行数据中的第".($k2+1)."列数据不存在");
					}
					$d[$i][$f['fieldname']]=$getfieldid;
				}
				if( $f['ifcheckexist'] && $d[$i][$f['fieldname']]!=""){
					if($_POST["catalog"]==1){
						$map=array();
						$map[$f['fieldname']]=$d[$i][$f['fieldname']];
						$c=$m->where($map)->count("*");
						if( $c > 0 )
						{
							unset($d[$k]);
							$errorStr[]=array("rowId"=>$k,"msg"=>"{columnIndex}".$k."中的数据已存在","columnIndex"=>$k2);//$this->error("第".($k)."行数据中的第".($k2+1)."列数据已存在");
						}
					}else if($_POST["catalog"]==2){
						if( !isset($d[$i]["id"]) ){
							$map=array();
							$map[$f['fieldname']]=$d[$i][$f['fieldname']];
							$id=$m->where($map)->getField("id");
							if($id) {
								$d[$i]["id"]=$id;
							}else{
								unset($d[$k]);
								$errorStr[]=array("rowId"=>$k,"msg"=>"{columnIndex}".$k."中的数据不存在","columnIndex"=>$k2);//$this->error("第".($k)."行数据中的第".($k2+1)."列数据不存在");
							}
						}
					}else if( $_POST["catalog"]==3){
						$map=array();
						$map[$f['fieldname']]=$d[$i][$f['fieldname']];
						$id=$m->where($map)->getField("id");
						if($id) {
							$d[$i]["id"]=$id;
						}
					}
					if(isset($uni[md5($d[$i][$f['fieldname']])])){
						unset($d[$k]);
						$errorStr[]=array("rowId"=>$k,"msg"=>"{columnIndex}".$k."中的数据与上传的文件中数据重复","columnIndex"=>$k2);//$this->error("第".($k)."行数据中的第".($k2+1)."列与上传的文件中数据重复");
					}
					$uni[md5($d[$i][$f['fieldname']])]=1;
				}
			}
			$i++;
		}
		$t=explode("_",$table);
		foreach($t as $k=>$v){
			$t[$k]=ucfirst(strtolower($v));
		}
	
		$table=implode("",$t);
		$model=D($table);
		$d2=$d;
	
		$fg_str = "\n";
		$max_count=1000;
		$tableName = $model->getTableName();
		$sql = $this->getbatchSql($tableName,$d_title,$d,$fg_str);
		$str=explode($fg_str,$sql);
		foreach($str as $k=>$v ){
			if(trim($v)){
				$model->query($v);
			}
		}
		if(count($errorStr)>1)
		{
			// 			$filetype =end(explode(".",$filename));
			// 			if($filetype=="xls"){
			// 				$inputFileType = 'Excel5';
			// 			}else if( $filetype=="xlsx"){
			// 				$inputFileType = 'Excel2007';
			// 			}
				
			// 			import('@.ORG.PHPExcel.IOFactory', '', $ext='.php');
			// 			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			// 			$objPHPExcel = $objReader->load($filename);
			// 			$currentSheet = $objPHPExcel->getActiveSheet();
			// 			$allColumn = $currentSheet->getHighestColumn();
			// 			$allRow = $currentSheet->getHighestRow();
				
			// 			$columnIndex = PHPExcel_Cell::columnIndexFromString($allColumn);
			// 			$maxColumnStr = PHPExcel_Cell::stringFromColumnIndex($columnIndex);
			// 			$currentSheet->setCellValue($maxColumnStr."1", "错误信息");
			// 			foreach($errorStr as $k => $v)
				// 			{
				// 				$id = $v["rowId"];
				// 				$columnStr = PHPExcel_Cell::stringFromColumnIndex($v["columnIndex"]);
				// 				$currentSheet->setCellValue($maxColumnStr.$id, str_replace("{columnIndex}",$columnStr,$v["msg"]));
				// 				$currentSheet->getStyle("A$id:$allColumn$id")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFFF0000");//设置错误行红色
				// 				$currentSheet->getStyle($maxColumnStr.$id)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFFFFF00");//设置错误提示列黄色
				// 				$currentSheet->getStyle($columnStr.$id)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFFFFF00");//设置错误列黄色
				// 			}
				// 			$filename = htmlspecialchars_decode($yuan_fileName."_log.".$filetype);
				// 			header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
				// 			header('Content-Disposition: attachment;filename='.($filename));
				// 			header('Cache-Control: max-age=0');
				// 			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $inputFileType);
				// 			$objWriter->save('php://output');
			$this->ajaxReturn(array("errorStr"=>$errorStr,"filename"=>$filename,"yuan_fileName"=>$yuan_fileName));
		}
		$this->success ( L('_SUCCESS_'));
	}

	/**
	 * @Title: commonimportAction
	 * @Description: todo(真实导入数据方法)
	 * @param unknown_type $fieldlist
	 * @param unknown_type $datalist
	 * @param unknown_type $volist
	 * @param unknown_type $excel
	 * @author liminggang
	 * @date 2013-6-1 上午11:19:23
	 * @throws
	 */
	private function commonimportAction($fieldlist,$datalist1,$volist,$excel,$filename="",$yuan_fileName="",$import_field,$modelname){
		$table = $volist['tableobj'];
		$errorStr = "";
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->importDataGetDetail ($modelname,1);
		$ifmustArr = array();
		$ifmustNameArr = array();
		$datalist = array();
		foreach($datalist1 as $k => $v){
			$datalist[] = $v;
		}
		foreach($detailList as $k => $v)
		{
			if($v["required"]){
				$ifmustArr[] = array("name"=>$v["name"],"showname"=>$v["showname"]);
				$ifmustNameArr[] = $v["name"];
			}
		}
		
		$msg = array();
		foreach($ifmustArr as $k => $v){
			$isGo = false;
			foreach($import_field as $kk => $vv){
				if($vv==$v["name"]){
					$isGo = true;
					break;
				}
			}
			if( ! $isGo){
				$msg[]=$v["showname"];
			}
		}
		if(count($msg)){
			$this->error(implode("、",$msg)." 为导入必选字段");
		}
		
		$d=$datalist;
		$i=0;
		$m=M($modelname);
		$sql = "";
		foreach($datalist as $k=>$v){
			foreach($v as $k2=>$v2 ){
				if(in_array($import_field[$k2],$ifmustNameArr) && empty($v2))
				{
					unset($d[$k]);
					$errorStr[]=array("rowId"=>$k,"msg"=>"{columnIndex}".$k."中的数据不能为空","columnIndex"=>$k2);//$this->error("第".($k)."行数据中的第".($k2+1)."列数据不能为空");
				}
				 
				if( ! empty($detailList[$import_field[$k2]]["unfunc"])){ //判断数据是否需要转换
					$unfunc = $detailList[$import_field[$k2]]["unfunc"][0];
					$unfuncdata = $detailList[$import_field[$k2]]["unfuncdata"][0];
					$dd = array();
					foreach($v as $kk => $vv){
						$dd[$import_field[$kk]] = $vv;
						unset($dd[$kk]);
					}
					$value = getConfigFunction($v2,$unfunc,$unfuncdata,$dd);
					if(empty($value))
					{
						unset($d[$k]);
						$errorStr[]=array("rowId"=>$k,"msg"=>"{columnIndex}".$k."中的数据不存在","columnIndex"=>$k2);
					}else{
						$d[$i][$k2]=$value;
					}
				}
				
				if(!empty($v2) && !empty($detailList[$import_field[$k2]]["validate"])){
					$validate = $detailList[$import_field[$k2]]["validate"];
					$bool = importValidate($validate,$v2);
					if(empty($value))
					{
						unset($d[$k]);
						$errorStr[]=array("rowId"=>$k,"msg"=>"{columnIndex}".$k."中的数据不存在","columnIndex"=>$k2);
					}
				}
				
				if($_POST["catalog"]==1){
					if($v2["unique"]=="1"){//判断数据是否允许重复
						$map=array();
						$map[$newImportField[$k2]]=$v2;
						$c=$m->where($map)->count("*");
						if( $c > 0 ) 
						{
							unset($d[$k]);
							$errorStr[]=array("rowId"=>$k,"msg"=>"{columnIndex}".$k."中的数据已存在","columnIndex"=>$k2);
						}
					}
				}else if($_POST["catalog"]==2){
					if( !isset($d[$i]["id"]) ){
						$map=array();
						$map[$f['fieldname']]=$d[$i][$f['fieldname']];
						$id=$m->where($map)->getField("id");
						
						if($id) {
							$d[$i]["id"]=$id;
						}else{
							unset($d[$k]);
							$errorStr[]=array("rowId"=>$k,"msg"=>"{columnIndex}".$k."中的数据不存在","columnIndex"=>$k2);
						}
					}
				}else if( $_POST["catalog"]==3){
					$map=array();
					$map[$f['fieldname']]=$d[$i][$f['fieldname']];
					$id=$m->where($map)->getField("id");
					if($id) {
						$d[$i]["id"]=$id;
					}
				}
				if(false){
					unset($d[$k]);
					$errorStr[]=array("rowId"=>$k,"msg"=>"{columnIndex}".$k."中的数据与上传的文件中数据重复","columnIndex"=>$k2);//$this->error("第".($k)."行数据中的第".($k2+1)."列与上传的文件中数据重复");
				}
			}
			$i++;
		}
		$model=D($modelname);
		$model->startTrans(); 
		$d2 = array();
		foreach($d as $k => $v){
			foreach($v as $kk => $vv){
				$d2[$k][$import_field[$kk]] = $vv;
				unset($d2[$k][$kk]);
			}
		}
		$fg_str = "\n";
		$max_count=1000;
		$tableName = $model->getTableName();
		$sql = $this->getbatchSql($tableName,$import_field,$d2,$fg_str);
		$str=explode($fg_str,$sql);
		$sqlCount = 0;
		foreach($str as $k=>$v ){
			if( ! empty($v)){
				$query=$model->query($v);
				$sqlCount++;
			}
		}
		$model->commit();
		if(count($errorStr)>1)
		{
			$this->ajaxReturn(array("errorStr"=>$errorStr,"filename"=>$filename,"yuan_fileName"=>$yuan_fileName,"sqlCount"=>$sqlCount));
		}
		$this->success (L('_SUCCESS_'));
	}
	
	public function export_error_excel(){
		$errorStr = $_POST['errorStr'];
		$filename = $_POST['filename'];
		$yuan_fileName = $_POST['yuan_fileName'];
		$sqlCount = $_POST['sqlCount'];
		$filetype =end(explode(".",$filename));
		if($filetype=="xls"){
			$inputFileType = 'Excel5';
		}else if( $filetype=="xlsx"){
			$inputFileType = 'Excel2007';
		}
		import('@.ORG.PHPExcel.IOFactory', '', $ext='.php');
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objPHPExcel = $objReader->load($filename);
		$currentSheet = $objPHPExcel->getActiveSheet();
		$allColumn = $currentSheet->getHighestColumn();
		$allRow = $currentSheet->getHighestRow();
		
		$columnIndex = PHPExcel_Cell::columnIndexFromString($allColumn);
		$maxColumnStr = PHPExcel_Cell::stringFromColumnIndex($columnIndex);
		$currentSheet->setCellValue($maxColumnStr."1", "错误信息");
		foreach($errorStr as $k => $v)
		{
			$id = $v["rowId"];
			$columnStr = PHPExcel_Cell::stringFromColumnIndex($v["columnIndex"]);
			$currentSheet->setCellValue($maxColumnStr.$id, str_replace("{columnIndex}",$columnStr,$v["msg"]));
			$currentSheet->getStyle("A$id:$allColumn$id")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFFF0000");//设置错误行红色
			$currentSheet->getStyle($maxColumnStr.$id)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFFFFF00");//设置错误提示列黄色
			$currentSheet->getStyle($columnStr.$id)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFFFFF00");//设置错误列黄色
		}
		
		$showfilename = htmlspecialchars_decode($yuan_fileName."_log.".$filetype);
		$save = explode(".",$filename);
		$file = explode(".",basename($filename));
		$downurl = __ROOT__."/Public/Uploads/".date("Ym")."/".$file[0]."_log.".$filetype;
		$savefilename = $save[0]."_log.".$filetype;
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $inputFileType);
		$objWriter->save($savefilename);
		$this->assign('downurl', $downurl); 
		$this->assign('showfilename', $showfilename);
		$this->assign('sqlCount', $sqlCount);
		$this->display();
	}
	
	/**
	 * @Title: getbatchSql
	 * @Description: todo(生成数据库批量新增或者批量更新语句)
	 * @param string $tableName    表名
	 * @param array  $titleList    表头
	 * @param array  $data    　　 要插入或更新的数据内容
	 * @param string $str    　　　分隔字符
	 * @author wangzhaoxia
	 * @date 2014-11-1
	 * @throws
	 */
	public function getbatchSql($tableName,$titleList,$data,$str="\n"){
		$sql_add_i=0;
		$sql_add = $str." INSERT INTO `".$tableName."` (`".implode("`,`", $titleList)."`) VALUES ";
		$index = 1;
		$sql_update_id=array();
		foreach($data as $k=>$v){
			if(empty($v["id"]))
			{
				if ($index % 1000 == 0) {
					$index = 1;
					$sql_add .= $str." INSERT INTO `".$tableName."` (`".implode("`,`", $titleList)."`) VALUES ";
				}
				if($sql_add_i>0&&$index>1) $sql_add.=",";
				$index++;
				$d_values = array();
				foreach($v as $kk=>$vv){
					$d_values[] = $vv;
				}
				$sql_add.="('".implode("','", $d_values)."')";
				$sql_add_i++;
			}
			else
			{
				$sql_update_id[$k]=$v["id"];
			}
		}
		
		foreach($data as $k=>$v){
			foreach($v as $kk=>$vv){
				if(empty($vv)&&isset($data[$k]["id"])) unset($data[$k][$kk]);
			}
		}
		$sql_update = $str." UPDATE `".$tableName."` SET ";
		foreach($data as $k=>$v){
			if(!empty($v["id"]))
			{
				$up_case_arr = array();
				foreach($v as $kk=>$vv){
					if($kk!="id")
					{
						$up_str = "`".$kk."` = CASE id";
						foreach($sql_update_id as $kkk=>$vvv){
							$up_str .= " WHEN ".$vvv." THEN '".$data[$kkk][$kk]."'";
						}
						$up_str .=" END";
						$up_case_arr[] = $up_str;
					}
				}
			}
		}
		$sql_update .=implode(",", $up_case_arr)." WHERE id IN (".implode(",", $sql_update_id).")";
		$sql = "";
		if($sql_add_i>0)
		{
			$sql .= $sql_add;
		}
		if(count($sql_update_id)>0)
		{
			$sql .= $sql_update;
		}
		return $sql;
	}
	
	/**
	 * @Title: comboxLinkfield
	 * @Description: todo(页面JS跳转或者数据展示到页面)
	 * @author liminggang
	 * @date 2013-6-1 上午11:19:46
	 * @throws
	 */
	public function comboxLinkfield(){
		$linktable = $_REQUEST['linktable'];
		$str = '[ ["", "请选择关联表更新字段"]';
		if ($linktable!='') {
			$model2=M("INFORMATION_SCHEMA.COLUMNS","","",1);
			$columnstitle = $model2->where("table_name = '".$linktable."' AND TABLE_SCHEMA = '".C('DB_NAME')."'")->getField("COLUMN_NAME,COLUMN_COMMENT");
			foreach ($columnstitle as $key => $value) {
				if($value) {
					$str = $str.', ["'.$key.'", "'.$value.'"]';
				} else {
					$str = $str.', ["'.$key.'", "'.$key.'"]';
				}
					
			}
		}
		$str = $str.' ]';
		echo $str;
	}
	/**
	 * @Title: comboxLinktableHtml
	 * @Description: todo(页面JS跳转或者数据展示到页面)
	 * @author liminggang
	 * @modified by jiangx date 2013-08-06 ajax请求select修改成chekfor IE ff 360 Chrome测试通过
	 * @date 2013-6-1 上午11:20:51
	 * @throws
	 */
	public function comboxLinktableHtml(){
		$num = $_POST['num'];
		$id = $_POST['id'];
		$model1=M("INFORMATION_SCHEMA.TABLES","","",1);
		$tabletitle = $model1->where("TABLE_SCHEMA = '".C('DB_NAME')."'")->getField("TABLE_NAME,TABLE_COMMENT");

		//$str = '<div class="unit tcg'.$num.'"><label>&nbsp;</label>';
		// 第一个select
		// 	$url = __URL__;
		// 	$str = $str.'<select num="'.$num.'" refurl="'.$url.'/comboxLinkfield/linktable/{value}" ref="import_combox_linkfield'.$num.'" name="linktable[]">';
		// 	$str = $str.'<option value="">请选择关联表</option>';
		// 	foreach($tabletitle as $k=>$v){
		//		if($v==""){
		//		    $tabletitle[$k]=$k;
		//		    $v=$k;
		//		}else{
		//		    $v=explode("; InnoDB",$v);
		//		    $tabletitle[$k]=$v[0];
		//		    if( count(explode("InnoDB",$v[0])) >1 ){
		//			    $v=$k;
		//			    $tabletitle[$k]=$k;
		//		    }
		//		}
		// 		$str = $str.'<option value="'.$k.'">'.$tabletitle[$k].'</option>';
		// 	}
		// 	$str = $str.'</select>';

		//<input type="text" callback="misimprotexcelsub_edit_ordernoLinkage" class="combox checkByInput" autocomplete="off" checkfor="Exnt_Tables"
		//	insert="TABLE_NAME" show="TABLE_NAME" value="{$tables[$vos1['linktable']]}" />
		//<input type="hidden"  name="linktable[]" value="{$vos1['linktable']}">
		//
		//
		//<input type="text" callback="misimprotexcelsub_edit_ordernoLinkage" class="combox checkByInput" autocomplete="off" checkfor="Exnt_Tables"
		//	insert="TABLE_NAME" show="TABLE_NAME"  <if condition="$vos1['linkfields'][$vos1['linkfield']]">value="{$vos1['linkfields'][$vos1['linkfield']]}" <else/>value="{$vos1['linkfield']}"</if>  />
		//<input type="hidden"  name="linkfield[]" value="{$vos1['linkfield']}">
		//
		$str = '<tr>';
		$str .= '<td>';
		$str .= '<input type="text" placeholder="关联表" callback="misimportexceledit_addassociationtable" class="required checkByInput textInput" autocomplete="off" checkfor="Exnt_Tables"
				insert="TABLE_NAME" show="TABLE_COMMENT" value="" />
				<input type="hidden" name="linktable[]" value="">';

		$str .= '</td><td>';

		$str .= '<input type="text" name="misimportexceledit_addassociationfield[]" placeholder="本表关联字段" class="required checkByInput textInput" autocomplete="off" checkfor=""
				insert="COLUMN_NAME" show="COLUMN_COMMENT"  value=""  />
				<input type="hidden" name="linkfield[]" value="">';

		$str .= '</td><td>';


		//// 第二个select
		//$str = $str.'<select num="'.$num.'" class="combox" name="linkfield[]" id="import_combox_linkfield'.$num.'">';
		//$str = $str.'<option value="">请选择关联表更新字段</option>';
		//$str = $str.'</select>';
		// 第三个select
		//$str = $str.'<select num="'.$num.'" class="combox" name="thefield[]">';
		$str = $str.'<select class="combox" name="thefield[]">';
		$str = $str.'<option value="">请选择本表关联字段</option>';
		$model2=D('MisImportExcelSub');
		$model3=D('MisImportExcel');
		$map['eid']=$id;
		if($id){
			$inheritid=$model3->where("id=.".$id)->getField("inheritid");
			if($inheritid){
				$map['eid']=$inheritid;
			}
		}
		$map['status']=array("egt",0);
		$sublist =$model2->where($map)->getField('fieldname,name');
		foreach ($sublist as $k => $v) {
			$str = $str.'<option value="'.$k.'">'.$v.'</option>';
		}
		$str = $str.'</select>';

		$str .= '</td><td>';

		$str = $str.'<span style="cursor:pointer;" class="info" onclick="dellinktable(this);">&nbsp;&nbsp;点击删除</span>';
			
		$str .= '</td>';
		$str .= '</tr>';
		//$str = $str."</div>";
		echo $str;
	}
}
?>