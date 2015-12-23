<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(车辆基本信息) 
 * @author chenxingjun 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-4-18 下午5:47:22 
 * @version V1.0
 */
class MisSystemCarAction extends CommonAction {
	/**
	 * @Title: _filter
	 * @Description: todo(构造检索条件)
	 * @param HASHMAP $map
	 * @author 杨东
	 * @date 2013-5-31 下午4:01:22
	 * @throws
	 */
	public function _filter(&$map){
		if ($_SESSION["a"] != 1){
			$map['status']=array('gt',0);  //0 状态被标记车辆有被申请
		}
	}
	public function index(){
		//查询所有的车辆
		$MisSystemCarDao=M('mis_system_car');
		if ($_SESSION["a"] != 1){
			$map['status']=array('gt',0);  //0 状态被标记车辆有被申请
		}
		$carlist=$MisSystemCarDao->where($map)->field('id,departmentID,carbelong')->select();
		//第一步构造左侧部门树结构(多封装一个公共用车和项目用车)
		$dptmodel = D("MisSystemDepartment");//部门表
		$deptlist = $dptmodel->where("status=1")->order("id asc")->field('id,name,parentid')->select();
		 
		foreach($deptlist as $key=>$val){
			$n=$m=0;
			if($carlist){
				foreach($carlist as $k=>$v){
					if($val['parentid']==0){
						$deptlist[$key]['count'] = count($carlist);
					}else{
						if($val['id'] == $v['departmentID']){
							$deptlist[$key]['count'] = ++$n;
						}else{
							$deptlist[$key]['count'] = $n;
						}
					}
				}
			}else{
				$deptlist[$key]['count'] = 0;
			}
		}
		$param['rel']="missystemcar";
		$param['url']="__URL__/index/jump/1/departmentID/#id#/parentid/#parentid#";
		$param['open']=true;
		$typeTree = $this->getTree($deptlist,$param);
		$this->assign('typetree',$typeTree);
		
		//右侧数据展示、查询所有车辆信息、列表过滤器，生成查询Map对象
		$map = $this->_search ();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		//如果是选中了部门查询，则带入部门 条件
		$departmentID=$_REQUEST['departmentID'];
		$parentid=$_REQUEST['parentid'];
		$carbelong=$_REQUEST['carbelong'];
		if($departmentID && $parentid){
			$map['departmentID']=$departmentID;
			$this->assign("departmentID",$departmentID);
			$this->assign("parentid",$parentid);
		}
		$name = $this->getActionName();
		if (! empty ( $name )) {
			$qx_name=$name;
			if(substr($name, -4)=="View"){
				$qx_name = substr($name,0, -4);
			}
			//验证浏览及权限
			if( !isset($_SESSION['a']) ){
				$map=D('User')->getAccessfilter($qx_name,$map);	
			}
			$this->_list ( $name, $map );
		}
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($name);
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		//扩展工具栏操作
		$toolbarextension = $scdmodel->getDetail($name,true,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		if( intval($_POST['dwzloadhtml']) ){
			$this->display("dwzloadindex");exit;
		}
		if($_REQUEST['jump']){
			$this->display('partList');
			exit;
		}
		$this->display ();
	}
	
	public function baseInfo(){
		//得到要找的车输信息， 通过ID
		$id= $_GET['carid'];
		$this->assign('carid',$id);
		
		$oil_id= $_GET['oilID'];
		$this->assign('oilID',$oil_id);
		
		$this->display();
	}
	
	/**
	 * 构造树形节点  @changeby wangcheng
	 * @param array  $alldata  构造树的数据
	 * @param array  $param    传入数组参数，包含rel，url【url中参数传递用#name#形式，name 为字段名字】
	 * @param array  $returnarr  初始化s树形节点，可以传入数字，默认选中哪一行数据
	 * @return string
	 */
	function getTree($alldata=array(),$param=array(),$returnarr=array()){
		foreach($alldata as $k=>$v){
			$newv=array();
			$matches=array();
			preg_match_all('|#+(.*)#|U', $param['url'], $matches);
			foreach($matches[1] as $k2=>$v2){
				if(isset($v[$v2])) $matches[1][$k2]=$v[$v2];
			}
			$url = str_replace($matches[0],$matches[1],$param['url']);
			$newv['id']=$v['id'];
			$newv['pId']=$v['parentid']?$v['parentid']:0;
			$newv['type']='post';
			if($v['carbelong'] == 2){
				$newv['url']="__URL__/index/jump/1/carbelong/2";
			}else{
				$newv['url']=$url;
			}
			$newv['target']='ajax';
			$newv['rel']=$param['rel'];
			$newv['title']=$v['name']; //光标提示信息
			$newv['name']=missubstr($v['name'],18,true)."(".$v['count'].")"; //结点名字，太多会被截取,针对于现在的ZTREE，宽度不能超过18个字符。
			if($v['parentid']==0){
				$newv['open']=$v['open'] ? $v['open']:'true';
			}
			array_push($returnarr,$newv);
		}
		return json_encode($returnarr);
	}
	
	/**
	 * 构造部门树形
	 * @param array  $alldata  所有部门信息
	 * @param int $pid  部门节点ID
	 * @param int $i  节点等级
	 * @return string
	 */
	private function getZtreelist($alldata){
		$returnarr=array();
		foreach($alldata as $k=>$v){
			$newv=array();
			$newv['id']=$v['id'];
			$newv['pId']=$v['parentid'];
			$newv['name']=$v['name'];
			$newv['type']='post';
			if($v['parentid']==0){ //最查层结点
				$newv['url']='__URL__/index/jump/1/parentid/'.$v['parentid'].'/departmentID/'.$v['parentid']; 
			}else if($v['id']>=9000){ //如果把部门下的车辆放到树中要用这个
				$newv['url']='__URL__/indexGroup/jump/1/parentid/'.$v['parentid'].'/departmentID/'.$v['parentid'].'/carid/'.$v['carid'];
			}else{  //列表部门出来
				$newv['url']="__URL__/index/jump/1/parentid/".$v['parentid']."/departmentID/".$v['id']."/carid/".$v['carid'];
			}
			
			//$param['url']="__URL__/index/jump/1/parentid/#parentid#/departmentID/#id#";  //  

			$newv['target']='ajax';
			$newv['rel']='missystemcar';
			$newv['open']='true';
			array_push($returnarr,$newv);
		}
		return json_encode($returnarr);
	}
	
	/**
	 * @Title: _before_add
	 * @Description: todo(打开新增前置函数)   
	 * @author 杨东 
	 * @date 2013-6-1 下午3:37:38 
	 * @throws 
	*/  
	public function _before_add(){
		//部门ID，装表单中的部门默认选种
		$departmentID = $_REQUEST['departmentID'];
		$this->assign('departmentID',$departmentID);
		
		//第一步构造部门(多封装一个公共用车和项目用车)
		$dptmodel = D("MisSystemDepartment");//部门表
		$deptlist = $dptmodel->where("status=1")->order("id asc")->field('id,name,parentid')->select();
		//第一个参数为所有部门，第二个参数为顶级节点，第三个参数为空格循环个数,第四个参数为默认选中
		$depthtml=$this->selectTree($deptlist,0,0,$departmentID);
		$this->assign('depthtml',$depthtml);
		
	    //订单号可写
	    $scnmodel = D('SystemConfigNumber');
        $writable= $scnmodel->GetWritable('mis_system_car');
   		$this->assign("writable",$writable);
   		
	   	//自动生成订单编号
	   	$code = $scnmodel->GetRulesNO('mis_system_car');
		$this->assign("code", $code);
		$this->getcartype();
	}
	/**
	 * @Title: getcartype 
	 * @Description: todo(获取车辆类型)   
	 * @author yuansl 
	 * @date 2014-7-15 下午4:34:55 
	 * @throws
	 */
	private  function getcartype(){
		$model = D("MisVehicleType");
		$CarTypeList = $model->where("status = 1")->select();
		$this->assign("TypeList",$CarTypeList);
	}
	/**
	 * @Title: _before_edit
	 * @Description: todo(打开修改页面前置函数)
	 * @author 杨东
	 * @date 2013-5-31 下午4:11:26
	 * @throws
	 */
	public function _before_edit(){
		//编号可写
	    $model1	=D('SystemConfigNumber');
	    $writable= $model1->GetWritable('mis_sales_project');
	    $this->assign("writable",$writable);
	    
		//查询车辆照片信息
		$mapatt["status"]  =1;
		$mapatt["orderid"] =$_REQUEST['id'];
		$mapatt["type"] =48;
		$m=M("mis_attached_record");
		$attarry=$m->where($mapatt)->select();
		$this->assign('attarry',$attarry);
		$this->assign('rel',$_REQUEST['rel']);
		$this->getcartype();
	}
	
	public function _after_edit($list){
		//第一步构造部门(多封装一个公共用车和项目用车)
		$dptmodel = D("MisSystemDepartment");//部门表
		$deptlist = $dptmodel->where("status=1")->order("id asc")->field('id,name,parentid')->select();
		//第一个参数为所有部门，第二个参数为顶级节点，第三个参数为空格循环个数,第四个参数为默认选中
		$depthtml=$this->selectTree($deptlist,0,0,$list['departmentID']);
		$this->assign('depthtml',$depthtml);
	}
	/**
	 * @Title: lookup
	 * @Description: todo(查询员工信息)   
	 * @author 杨东 
	 * @date 2013-6-1 下午3:59:26 
	 * @throws 
	*/  
	public function lookup() {
		$searchby=array(
			array("id" =>"mis_hr_personnel_person_info-name","val"=>"按员工姓名"),
			array("id" =>"mis_hr_personnel_person_info-orderno","val"=>"按员工编号"),
		);
		$searchtype=array(
				array("id" =>"2","val"=>"模糊查找"),
				array("id" =>"1","val"=>"精确查找")
		);
		$this->assign("searchtypelist",$searchtype);
		$this->assign("searchbylist",$searchby);
		//检索部分
		if(!empty($_POST['keyword'])){
			$searchtypes=	$_POST['searchtype'];
			$searchbys	= 	str_replace("-",".",$_POST['searchby']);
			$keywords	=	$_POST['keyword'];
			$map[$searchbys]=$searchtypes==2 ? array('like',"%".$keywords."%"):$keywords;
			//保留状态
			$this->assign('keyword',$keywords);
			$searchbys	= 	str_replace(".","-",$_POST['searchby']);
			$this->assign('searchby',$searchbys);
			$this->assign('searchtype',$searchtypes);
		}
		$map['mis_hr_personnel_person_info.status']=1;
		$this->_list('MisHrBasicEmployeeView',$map);
		
		$this->display("lookup");

	}
	/**
	 * @Title: indexGroup
	 * @Description: todo(车国所有相关信息索引页面)   
	 * @author 杨东 
	 * @date 2013-6-1 下午3:59:45 
	 * @throws 
	*/  
	public function detail(){
		//得到要找的车输信息， 通过ID
		$id= $_GET['carid'];
		$this->assign('carid',$id);
		//加油记录，用oilID去索引oil_id
		$oil_id= $_GET['oilID'];
		$this->assign('oilID',$oil_id);
		$name = $this->getActionName();
		//建立模型
		$model= D($name);
		$list = $model->find($id);
// 		dump($list);
		$this->assign('vo',$list);
		//获取当月最新的一条加油维护记录，记录表在MisCarAddOilInfo表中
		$mapMCAOI['car_id'] = $id;
		$modelMCAOI= M('MisCarAddOilInfo');
		$minScore = $modelMCAOI->where($mapMCAOI)->order('id desc')->find();
		$this->assign('vooil',$minScore);
		
		//获取上传图片列表
		$map["status"]  =1;
		$map["orderid"] =$id;
		$map["type"] =48;
		$m=M("mis_attached_record");
		$attarry=$m->where($map)->select();
		$this->assign('attarry',$attarry);
		// 显示图片的路径
		$codeValue = $_REQUEST["code"];
		$path = '../Public/Uploads/carpic/'.$codeValue."/";
		
		//传到方法中去拿到目录下的所有图片
		$pic = $this->getPic($path);
		
		//把这个$path分配到模板当中， 如果没有图片时显示一下图片
		$this->assign('noPicPath','/Public/Uploads/carpic');
		//反回的图片信息数据， 分配到模板中去
		$this->assign('pic',$pic);
		$this->display();
	}
	/**
	 * @Title: partList
	 * @Description: todo(所有部门)   
	 * @author 杨东 
	 * @date 2013-6-1 下午4:02:34 
	 * @throws 
	*/  
	public function partList(){
		//得到要找的车输信息， 通过ID
		$id= $_GET['fid'];
		//把它付值给，新境按钮
		$this->assign ( 'fid', $id );
		
		if ($_SESSION["a"] != 1) $map['status']=1;
		
		//如果$id等于0 或都  设定了值时才会在查询里加入条件，部门ＩＤ等于，传过来的值。
		if($id || $id=='0') $map['departmentID'] =$id;
		$name = $this->getActionName();
		//建立模型
		//$model= D($name);
		$this->_list($name,$map);
		//$list = $model->where($map)->select();
		//$this->assign('list',$list);
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($name);
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		//searchby搜索扩展
		$searchby = $scdmodel->getDetail($name,true,'searchby');
		if ($searchby && $detailList) {
			$searchbylist=array();
			foreach( $detailList as $k=>$v ){
				if(isset($searchby[$v['name']])){
					$arr['id']= $searchby[$v['name']]['field'];
					$arr['val']= $v['showname'];
					array_push($searchbylist,$arr);
				}
			}
			$this->assign("searchbylist",$searchbylist);
		}
		//扩展工具栏操作
		$toolbarextension = $scdmodel->getDetail($name,true,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		$this->display();
	}
	/**
	 * @Title: _before_update
	 * @Description: todo(修改保存前置函数(上传图片代码))   
	 * @author 杨东 
	 * @date 2013-6-1 下午4:08:25 
	 * @throws 
	*/  
	public function _before_update(){

		//调用上传基本设定共用部份
		//$this->uploadSetupEagle();
		$this->swf_upload($_REQUEST['id'],48);

	}
	/**
	 * @Title: _after_insert
	 * @Description: todo(新增插入后置函数) 
	 * @param unknown_type $id  
	 * @author 杨东 
	 * @date 2013-6-1 下午4:08:58 
	 * @throws 
	*/  
	public function _after_insert($id){
		$this->swf_upload($id,48);
	}
	
	/* (non-PHPdoc)上传
	 * @see CommonAction::upload()
	 */
	public function upload($path=''){
		import ( '@.ORG.UploadFile' );
		$upload = new UploadFile(); // 实例化上传类
		$upload->maxSize = C('maxSize') ; // 设置附件上传大小 3MB
		$upload->allowExts = array('jpg', 'gif', 'png', 'jpeg'); // 上传类型设置
		$upload->savePath = $path? $path:C('savePath'); // 上传目录
		$upload->saveRule2=true;
		$upload->autoSub = false; // 是否开启子目录
		if(!$upload->upload()) { // 上传错误提示错误信息
		    $this->error($upload->getErrorMsg());
		}
	}
	
	/*****
	*   $path  传个值过来一个咱径。 
	*   功能： 显示图片相关
	*/
	private function getPic($path){
		$tem =$output= array();
		$opath = str_replace('../Public', '__PUBLIC__', $path);
		if(!file_exists($path)){
			return false;
		}else{
			foreach (new DirectoryIterator($path) as $file) {
				if(!$file->isDot()){
					$arr['path']=$opath.'/'. $file->getFilename()."?rand=".mt_rand();
					$arr['file']=$file->getFilename();
					array_push($tem,$arr);
				}
			}
		}
		$imglength = count($tem);
		if($imglength>=5){
			$output = array_slice($tem, 0,5);
		}elseif($imglength<5 && $imglength>0){
			$output = $tem;
		}
		return $output;
	}
	/**
	 *
	 * @Title: lookupmanage
	 * @Description: todo(用ztree形式查询出所有部门员工信息)
	 * @author liminggang
	 * @throws
	 */
	public function lookupmanage(){
		$model= M('mis_system_department');
		$deptlist = $model->where("status=1")->order("id asc")->select();
		$param['rel']="missystemcarBox";
		$param['url']="__URL__/lookupmanage/jump/1/deptid/#id#/parentid/#parentid#";
		$common=A("Common");
		$typeTree = $common->getTree($deptlist,$param);
		$this->assign('tree',$typeTree);
		$map = array();
		$searchby = str_replace("-", ".", $_POST["searchby"]);
		$keyword=$this->escapeChar($_POST["keyword"]);
		$searchtype = $_POST['searchtype'];
		if($_POST["keyword"]){
			$map[$searchby] = ($searchtype==2)  ? array('like','%'.$keyword.'%'):$keyword;
			$this->assign('keyword',$keyword);
			$searchby = str_replace(".", "-", $_POST["searchby"]);
			$this->assign('searchby',$searchby);
			$this->assign('searchtype',$searchtype);
		}
		$searchby=array(
			array("id" =>"mis_hr_personnel_person_info-name","val"=>"按员工姓名"),
			array("id" =>"orderno","val"=>"按员工编号"),
		);
		$searchtype=array(array("id" =>"2","val"=>"模糊查找"),
				array("id" =>"1","val"=>"精确查找"));
		$this->assign("searchbylist",$searchby);
		$this->assign("searchtypelist",$searchtype);
		$map['working'] = 1; //在职
		$map['mis_hr_personnel_person_info.status'] = 1; //正常
		$deptid		= $_REQUEST['deptid'];
		$parentid	= $_REQUEST['parentid'];
		if ($deptid && $parentid) {
			$deptlist =array_unique(array_filter (explode(",",$this->findAlldept($deptlist,$deptid))));
			$map['mis_hr_personnel_person_info.deptid'] = array(' in ',$deptlist);
		}
		$this->_list('MisHrBasicEmployeeView',$map);
		$this->assign('deptid',$deptid);
		$this->assign('parentid',$parentid);
		if ($_REQUEST['jump']) {
			$this->display('lookupmanagelist');
		} else {
			$this->display("lookupmanage");
		}
	}
	/**
	 * @Title: common
	 * @Description: todo(公共函数)   
	 * @author 杨东 
	 * @date 2013-6-1 下午4:33:56 
	 * @throws 
	*/  
	private function common(){
		$mismodel=D("MisSystemDepartment");
		$mislist =$mismodel->where('status = 1')->field("id,name")->select();
		$this->assign("department_idlist",$mislist);
	}
	/**
	 * @Title: insurance模块
	 * @Description: todo(保险记录)   
	 * @author  eagle
	 * @date 2013-6-1 下午4:33:56 
	 * @throws 
	*/ 
	function insurance(){
		$insuranceMODEL= A('MisCarInsurance');
		$list = $insuranceMODEL->_filter;
		$list = $insuranceMODEL->_list('MisCarInsurance');
		$insuranceMODEL->display('MisCarInsurance:index');
		//$this->display('MisCarInsurance:index');
		
	}

}
?>