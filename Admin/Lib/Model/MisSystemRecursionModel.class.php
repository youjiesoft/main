<?php
/**
 * @Title: MisSystemFlowFormTestModel 
 * @Package package_name 
 * @Description: 项目模板-资源获取页面
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-8-16 上午11:04:15 
 * @version V1.0
 */
/**
 * example
$model=D('MisSystemRecursion');
$a=$model->modelShow('MisSystemTest',array('key'=>'a','pkey'=>'b','fields'=>"a,b,c",'conditions'=>'b>=0'),0,1);
$level=$model->getMaxLevel();
$levelGroup=$model->getLevelGroup();
print_r($result);
print_r($level);
exit;
*/
class MisSystemRecursionModel extends CommonModel {
	//protected $trueTableName = 'mis_system_flow_form';

	
	private $info=array();//初始化插入数据
	private $infoTopNode=array();//顶级ID数组
	private $infoTop=array();//顶级
	private $infoSubNode=array();//下级ID数组
	private $infoSub=array();//下级
	private $infoNew=array();//新数组
	private $checkArr=array();//校验数组
	private $num=0;//重置数据标识
	private $reordering=0;//数据重排序
	private $nextEnd=0;//根节点标示
	private $formKey="";//插入主键
	private $formPkey="";//插入父类主键
	private $toKey="";//插入主键
	private $toPkey="";//插入父类主键
	private $parentidArr=array();//父类ID数组
	private $maxLevel=1;//最高层级
	private $formPkeyVal=0;//根节点值
	
	//初始化数据
	public function __construct(){
		//初始化化
		$this->info=array();//初始化插入数据
		$this->infoTopNode=array();//顶级ID数组
		$this->infoTop=array();//顶级
		$this->infoSubNode=array();//下级ID数组
		$this->infoSub=array();//下级
		$this->infoNew=array();//新数组
		$this->checkArr=array();//校验数组
		$this->num=0;//重置数据标识
		$this->reordering=0;//数据重排序
		$this->nextEnd=0;//根节点标示
		$this->formKey="";//插入主键
		$this->formPkey="";//插入父类主键
		$this->toKey="";//插入主键
		$this->toPkey="";//插入父类主键
		$this->parentidArr=array();//父类ID数组
		$this->maxLevel=1;//最高层级
		$this->formPkeyVal=0;//根节点值
	}
	//数组递归新增
	public  function dataAdd($data,$dataParam,$formModel,$formModelParam,$reordering=0,$extendInfo=0){
		$this->main(1,$data,$dataParam,'','',$formModel,$formModelParam,$reordering,$extendInfo);
	}
	//数组递归展示
	public  function dataShow($data,$dataParam,$reordering=0,$extendInfo=0){
		$result=$this->main(2,$data,$dataParam,'','','','',$reordering,$extendInfo);
		return $result;
	}
	//模型递归新增
	public  function modelAdd($formModel,$formModelParam,$formModel,$formModelParam,$reordering=0,$extendInfo=0){
		$this->main(1,'','',$formModel,$formModelParam,$toModel,$toModelParam,$reordering,$extendInfo);
	}
	//模型递归展示
	public  function modelShow($formModel,$formModelParam,$reordering=0,$extendInfo=0){
		$result=$this->main(2,'','',$formModel,$formModelParam,'','',$reordering,$extendInfo);
		return $result;
	}
	

	/**
	 * @Title: main
	 * @Description: todo(主方法)
	 * @param $Type  1、数据插入     2、数据展示
	 * @param $reordering 重排序 1为是，0为否
	 * @param $extendInfo   扩展属性压入数组，1；节点层级  2；是否根节点
	 * @param $data      来源数据
	 * @param $formModel 来源模型
	 * @param $formModelParam 来源模型参数
	 * @param $toModel   插入模型
	 * @param $toModelParam   目标模型参数
	 * @param $toModel
	 * @author yangxi
	 * @date 2014-10-21 上午11:00:00
	 * @throws
	 */
	
	function main($type,$data,$dataParam,$formModel,$formModelParam,$toModel,$toModelParam,$reordering=0,$extendInfo=0){
		//获取扩展信息
		$extendInfo?$this->extendInfo=1:$this->extendInfo=0;
		//重排序定义
		$reordering?$this->reordering=1:$this->reordering=0;
		if(!$type && !$data && !$formModel) return false;
		//获取来源对象，data优先与formModel的结果集，将忽略formModel
		if($data){
			$dataParam['key']? $this->formKey=$dataParam['key']: $this->formKey='id';
			$dataParam['pkey']? $this->formPkey=$dataParam['pkey']: $this->formPkey='parentid';
			$dataParam['pkeyVal']? $this->formPkeyVal=$dataParam['pkeyVal']: $this->formPkeyVal=0;
		}else if($formModel){
			//实例化来源的的模型对象
			$formModel=D($formModel);
			//对传入参数做处理
			$formModelParam['key']?$this->formKey=$formModelParam['key']:$this->formKey='id';
			$formModelParam['pkey']?$this->formPkey=$formModelParam['pkey']:$this->formPkey='parentid';
			$formModelParam['pkeyVal']?$this->formPkeyVal=$formModelParam['pkeyVal']:$this->formPkeyVal=0;
			$formModelParam['conditions']? $conditions=$formModelParam['conditions']:"";//传入字符串查询条件
			$formModelParam['fields']? $fields=$this->formKey.",".$this->formPkey.",".$formModelParam['fields']:"";//传入要查询的字符串
			$Where = " 1 = 1 ";
			if($conditions) $Where .= " AND ".$conditions;
			if($fields){
				$data=$formModel->where($Where)->field($fields)->select();	
				//$data=$formModel->field($fields)->select();	
			}else{
				$data=$formModel->where($conditions)->select();
			}
			
		//print_r($formModel->getLastSql());
		}
		//判断类型执行
		switch($type){			
		case 1:
			if(!$formModel) return false;
			//实例化要插入的模型对象
			$toModel=D($toModel);
			//匹配要插入的key与pkey
			$toModelParam['key']?$this->toKey=$toModelParam['key']:$this->toKey='id';
			$toModelParam['pkey']?$this->toPkey=$toModelParam['pkey']:$this->toPkey='parentid';

	// 		print_r($result);
			$toModel->startTrans();
			//获取要插入表最新的ID
			$maxId=$toModel->max('id');
			if(empty($maxId)){
				$maxId=0;
			}
			//print_R($this->infoSub);
			$this->DFS($data,$maxId);
			//print_r($this->infoNew);
			//数据插入项目
			$toModel->addAll($this->infoNew);
			$toModel->commit();
			break;
		case 2:
			//$this->toKey=$this->formKey;
			//$this->toPkey=$this->formPkey;
			$this->toKey='id';
			$this->toPkey='parentid';
			$dataParam['num']?$maxId=$dataParam['num']-1:$maxId=0;
			$this->DFS($data,$maxId);
			//返回行数组
			return $this->infoNew;
			break;
		
		}
	}
	//深度优先遍历
	//@data          带邻接表的二维数组结构
	//@paramate num  最新排序
	function DFS($data,$num){
		$this->num=$num+1;
		if($this->num<=0){$this->num=1;}
		foreach($data as $key =>$val){
			if($val[$this->formPkey]==$this->formPkeyVal){
				$this->infoTop[]=$val;
				$this->infoTopNode[]=$val[$this->formKey];
				$this->parentidArr[]=$val[$this->formPkey];
			}else{
				$this->infoSub[]=$val;
				$this->infoSubNode[]=$val[$this->formKey];
				//获取父类ID数组
				$this->parentidArr[]=$val[$this->formPkey];
			}
		}
		$this->parentidArr=array_unique($this->parentidArr);
		foreach($this->infoTopNode as $key => $val){
			//创建校验数组
			$this->checkArr[$this->infoTop[$key][$this->formKey]]=$this->num;
			//不重排序
			if($this->reordering){
			//重新对ID赋值
			$this->infoTop[$key][$this->formKey]=$this->num;
			}
			//是否存在于父id序列中，存在为1，不存在为0
			if(in_array($this->infoTop[$key][$this->formKey],$this->parentidArr)){
				$sign=1;
			}else{
				$sign=0;
			}
			//是否具有扩展属性
			if($this->extendInfo==1){
				//当存在于父id序列中，那就不为根节点
				if($sign){
				   $this->infoTop[$key]["nextEnd"]=0;
				}else{
					//当存在于父id序列中，那就为根节点
					$this->infoTop[$key]["nextEnd"]=1;
				}
				$this->infoTop[$key]["level"]=1;
			}
			
			//如果为根节点，直接压入新数组
			array_push($this->infoNew,$this->infoTop[$key]);
			//销毁掉已验证的数据
			unset($this->infoTopNode[$key]);

			//进入子节点递归方法
			$this->recursion($val,2);
			$this->num++;
		}	
//   	print_r($this->checkArr);
// 		print_r($this->infoNew);
	}
	/**
	 * @Title: recursion
	 * @Description: todo(递归子层级信息)
	 * @param $val  当前父ID值
	 * @param $level 当前级别
	 * @author yangxi
	 * @date 2014-10-21 上午11:00:00
	 * @throws
	 */	
	function  recursion($val,$level){
		if($level>10){
			return ;
		}
		//开始循环子类节点
		foreach($this->infoSubNode as $subkey=>$subval){
			//如果某个子节点的父ID与当前查询节点ID相同
			if($this->infoSub[$subkey][$this->formPkey]==$val){
				//如果在校验数组内存在，则获取校验数组最新值							
					if(array_key_exists($this->infoSub[$subkey][$this->formPkey],$this->checkArr)){	
						//echo $this->infoSub[$subkey][$this->formPkey]."<br/>";
						//重排序
						$this->num++;
						//校验数组
						$this->checkArr[$this->infoSub[$subkey][$this->formKey]]=$this->num;												
	
							if($this->reordering){
							    //数组ID新赋值
								$this->infoSub[$subkey][$this->formKey]=$this->num;
								$this->infoSub[$subkey][$this->formPkey]=$this->checkArr[$this->infoSub[$subkey][$this->formPkey]];
							}

							//最大级别
							if($this->maxLevel<=$level){
								$this->maxLevel=$level;
							}
							//是否存在于父id序列中，存在为1，不存在为0
							if(in_array($this->infoSub[$subkey][$this->formKey],$this->parentidArr)){
								$sign=1;
							}else{
								$sign=0;
							}
							if($this->extendInfo==1){
								if($sign){
									$this->infoSub[$subkey]["nextEnd"]=0;	//是父级，不属于末级
								}else{
									$this->infoSub[$subkey]["nextEnd"]=1;   //是子级，不属于末级
								}
								$this->infoSub[$subkey]["level"]=$level;
							}							
							//插入数据
							$infoNewNum=array_push($this->infoNew,$this->infoSub[$subkey]);
							//销毁掉已验证的数据
							unset($this->infoSubNode[$subkey]);
                            if($sign==0){ continue; }
							//继续向下递归是否存在
							$this->recursion($subval,$level+1);
						}
					}else{					
                         continue;
					}						
		}
	}
	
	/**
	 * @Title: getMaxLevel
	 * @Description: todo(获取最高级别)
	 * @默认按$this->maxLevel作为最高级别；
	 * @return 返回最高级别
	 * @author yangxi
	 * @date 2014-10-21 上午11:00:00
	 * @throws
	 */	
	public function getMaxLevel(){
		return $this->maxLevel;
	}
	/**
	 * @Title: getLevelGroup
	 * @Description: todo(获取按级别分组的结果)
	 * @默认按$this->infoNew作为元素数据处理；如果为空，返回false
	 * @return $result 根据级别分组后的数据
	 * @author yangxi
	 * @date 2014-10-21 上午11:00:00
	 * @throws
	 */	
	public function getLevelGroup(){	
		if(empty($this->infoNew)){	
			return false;
		}else{
			$result=array();
			foreach($this->infoNew as $key =>$value ){
				$result[$value['level']-1][]=$value;
			}
			return $result;
		}
	}
}
?>