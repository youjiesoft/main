<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(selectlist配置文件) 
 * @author libo 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-7-28 下午2:20:04 
 * @version V1.0
 */
class LookupObjModel extends CommonModel{
	protected $trueTableName = "mis_system_lookupobj";
	protected $autoCheckFields = false;
	
// 	public $_auto	=array(
// 			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
// 			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
// 			array('createtime','time',self::MODEL_INSERT,'function'),
// 			array('updatetime','time',self::MODEL_UPDATE,'function'),
// 			//array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
// 			//array('departmentid','getDeptID',self::MODEL_INSERT,'callback'),
// 	);
// 	public $_validate=array(
// 			array('title','','名称已存在！',self::MUST_VALIDATE,'unique',self::MODEL_BOTH),
// 	);
	public function SetRules($data=array()){
		$filename=  $this->GetFile();
		$this->writeover($filename,"return ".$this->pw_var_export($data).";\n",true);
	}
	/**
	 * @Title: SetSongleRule
	 * @Description: todo(未知文件名称) 
	 * @param string $filename 文件路径名称
	 * @param array $data  数据
	 * @author 谢友志 
	 * @date 2015-1-26 下午5:29:28 
	 * @throws
	 */
	public function SetSongleRule($filename,$data=array()){
	  return	$this->writeover($filename,"return ".$this->pw_var_export($data).";\n",true);
	}
	
	public function GetRules($keyVal = '') {
		$value = '';
		if (file_exists($this->GetFile())) {
			$selectlist = require $this->GetFile();
			foreach ($selectlist as $key => $val) {
				if ($key) {
					if ($key==$keyVal) {
						$value = $val;
					}
				}
	
			}
		}
		return $value;
	}
	public  function GetFile(){
		return  DConfig_PATH."/System/lookupobj.inc.php";
	}
	/**
	 * @Title: GetFile
	 * @Description: todo(获取配置文件路径 ) 
	 * @return string  
	 * @author 谢友志 
	 * @date 2015-1-26 下午5:27:30 
	 * @throws
	 */
	public function GetDetailsPath(){	
		return  Dynamicconf."/LookupObj/Details";
	}

	/**
	 * @Title: GetFile
	 * @Description: todo(获取配置索引文件路径 )
	 * @return string
	 * @author 谢友志
	 * @date 2015-1-26 下午5:27:30
	 * @throws
	 */
	public function GetConflistFile(){
		return  Dynamicconf."/LookupObj/Index/list.inc.php";
	}
	/**
	 * @Title: GetLookupIndex
	 * @Description: todo(获取索引数据) 
	 * @return multitype:  
	 * @author 谢友志 
	 * @param string $status 是否排除状态为空的记录 默认不排除
	 * @date 2015-1-24 下午8:53:42 
	 * 暂时忽略数组与文件不对应的情况
	 * @throws
	 */
	public function GetLookupIndex($status=false){
		$file = Dynamicconf."/LookupObj/Index/list.inc.php";
		$list = array();
		if(file_exists($file)){
			$list = require $file;
			if($status){
				if($list){
					foreach($list as $k=>$v){
						$file = $this->GetDetailsPath().'/'.$k.'.php';
						if(file_exists($file)){
							$temp = require $file;
							if(!$temp['status']){
								unset($list[$k]);
							}
						}
					}
				}
			}
		}
		return $list;
	}
	/**
	 * @Title: GetLookupDetails
	 * @Description: todo(获取全部可读的配置) 
	 * @return multitype:unknown   
	 * @author 谢友志 
	 * @date 2015-1-24 下午8:53:07 
	 * @throws
	 */
	public function GetLookupDetails(){
		$index = $this->GetLookupIndex();
		$details = array();
		if($index){
			foreach($index as $k=>$v){
				$file = $this->GetDetailsPath().'/'.$k.'.php';
				if(file_exists($file)){
					$temp = require $file;
					if($temp['status']){
						$details[$k] = $temp;
					}					
				}
			}
		}
		return $details;
	}
	/**
	 * @Title: GetLookupDetail
	 * @Description: todo(获取指定的一个配置) 
	 * @param unknown_type $id
	 * @return multitype:  
	 * @author 谢友志 
	 * @date 2015-1-24 下午10:09:18 
	 * @throws
	 */
	public function GetLookupDetail($id){
		$file =  Dynamicconf."/LookupObj/Details/".$id.'.php';
		$list=array();
		if(file_exists($file)){
			$list = require $file;
		}
		return $list;
	}
	/**
	 * @Title: createOneDetail
	 * @Description: todo(根据数据库一条记录生成相应配置文件) 
	 * @param unknown_type $id  
	 * @author 谢友志 
	 * @date 2015-4-22 下午4:33:53 
	 * @throws
	 */
	public function createOneDetail($id){
		$list = $this->GetLookupDetail($id);
		$resource = $this->where("status=1 and id='{$id}'")->find();
		if(!$list){
				
		}
		$conlist [$resource ['id']] = array (
				'title' => $resource ['title'],
				'fields' => $resource ['fields'],
				'fields_china' => $this->getDeatilshowname ( $resource ['mode'], $resource ['fields'], '', 'china' ),
				'checkforfields' => $resource ['checkforfields'],
				'fieldcom' => $resource ['fieldcom'],
				'listshowfields' => $resource ['listshowfields'],
				'listshowfields_china' => $this->getDeatilshowname ( $resource ['mode'], $resource ['listshowfields'], '', 'china' ),
				'funccheck' => $resource ['funccheck'],
				'funccheck_china' => $this->getDeatilshowname ( $resource ['mode'], $resource ['funccheck'], '', 'china' ),
				'funcinfo' => unserialize ( base64_decode ( $resource ['funcinfo'] ) ),
				'url' => $resource ['url'],
				'mode' => $resource ['mode'], // 屈强@2014-08-07 新增标题项
				'checkformodel' => $resource ['checkformodel'],
				'filed' => $resource ['filed'],
				'filed1' => $resource ['filed1'],
				'val' => $resource ['val'],
				'showrules' => $resource ['showrules'],
				'rulesinfo' => $resource ['rulesinfo'],
				'rules' => $resource ['rules'],
				'viewname' => $resource ['viewname'],
				'viewtype' => $resource ['viewtype'],
				'condition' => $resource ['rules'],
				'dialogwidth' => $resource ['dialogwidth'],
				'dialogheight' => $resource ['dialogheight'],
				'status' => $resource ['status'],
				'level' => $resource ['level']
		) ;// 屈强@2014-08-07 新增当前项权限
		if($resource['datetable']){
				$conlist['proid']=$resource['proid'];
				$conlist['fieldback']=$resource['fieldback'];
				//$conlist[$v['id']]['fieldcom']=$v['fieldcom'];
				$conlist['dt']=unserialize(base64_decode($resource['datetable']));
			}
			//$model->SetRules($conlist);
			$path = $this->GetDetailsPath();
			$filename = $path.'/'.$resource['id'].".php";
			$this->SetSongleRule($filename,$conlist);
			$fileindex[$resource['id']] = $resource['title'];
			$conflistfile = $this->GetConflistFile();
			$conflist = require $conflistfile;
			$conflist = array_merge($conflist,$fileindex);
			$this->SetSongleRule($conflistfile,$conflist);
				
		
		
	}
	private function getDeatilshowname($model,$name,$type,$return){
		if(!$type){
			$name=explode(",",$name);
		}
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($model,false);
		$shownamechina = array();
		$showname=array();
		$shownamelist=array();
		if($return!='china'){
			foreach ($detailList as $dlist=>$dval){
				foreach ($name as $key=>$val){
					if($dval['name']==$val){
						$shownamechina[$val]= $dval['showname']?$dval['showname']:$dval['name'];
						$shownamelist[]=$dval['showname'];
						$showname[]="&#39;".$dval['name']."&#39;=>&#39;".$dval['showname']."&#39;";
							
					}
				}
			}
		}else{
			foreach ($name as $key=>$val){
				$shownamechina[$val]= $val;
				foreach ($detailList as $dlist=>$dval){
					if($dval['name']==$val){
						$shownamechina[$val]= $dval['showname']?$dval['showname']:$dval['name'];
						$shownamelist[]=$dval['showname'];
						$showname[]="&#39;".$dval['name']."&#39;=>&#39;".$dval['showname']."&#39;";
							
					}
				}
			}
		}
	
		//$showname[]="&#39;id&#39;=>&#39;编号&#39;";
		if($return == 'china'){
			return $shownamechina;
		}elseif($type){
			return $showname;
		}else{
			return $shownamelist;
		}
	}
}

?>