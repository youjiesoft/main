<?php
/**
 * @Title: MisDynamicFormBaseAction
 * @Package package_name
 * @Description: todo(动态表单基类)
 * @author quqiang
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2014-10-14 下午07:45:57
 * @version V1.0
 */
class MisDynamicFormBaseAction extends CommonAction{

	/**
	 * 组件私有属性列表
	 * @var array('caotegory'=>'property{}')
	 */
	protected $privateProperty;

	/**
	 * 公用属性列表
	 * @var array('property'=>'val ,val')
	 */
	protected $publicProperty;

	/**
	 * 组件信息列表
	 * @var array
	 */
	protected $controls;
	/**
	 * 组件配置文件信息
	 * @var array
	 */
	protected $controlConfig;
	/**
	 * 表单名称
	 * @var string
	 */
	protected $nodeName = '';

	/**
	 * 表单标题
	 * @var string
	 */
	protected $nodeTitle = '';

	/**
	 * 最终的表名
	 * @var string
	 */
	protected $tableName = '';

	/**
	 * 审批流使用字段
	 * @var array
	 */
	protected $auditField;

	/**
	 * 默认字段
	 * @var array
	 */
	protected $defaultField;

	/**
	 * 基础档案字段
	 * @var array
	 */
	protected $baseArchivesField;
	/**
	 * 系统使用的字段
	 * @var array
	 */
	protected $systemUserField;

	/**
	 * 是否是审核
	 * @var boolean
	 */
	protected $isaudit;

	/**
	 * 是否为只记录无代码生成
	 * @var boolean
	 */
	protected $isrecord;
	
	/**
	 * 是否生成数据表
	 * @var boolean
	 */
	protected $inserttable;
	/**
	 * lookup的属性可用属性列表
	 * @var array
	 */
	protected $lookupConfig;

	protected $checkforConfig;
	/**
	 * 组件的默认值
	 * @var array
	 */
	protected $controlDefaultVal;

	/**
	 * 简单配置文件中属性与现有组件属性的对应关系
	 */
	protected $confRelField;

	/**
	 * 当前节点
	 * @var string
	 */
	protected $curnode;

	/**
	 * 模板生成方式集合
	 * @var Array
	 */
	protected $tpltypeArr;

	/**
	 * 选取的模板生成方式
	 * @var string
	 */
	protected $tpltype;

	/**
	 * 单位对应关系数组
	 * @var array
	 */
	protected $untils;
	/**
	 * 表单类型
	 * @var string
	 */
	protected $formtype;
	/**
	 * 当前模型list配置文件
	 * @var array
	 */
	protected $datalist;
	
	function __construct(){
		parent::__construct();
		$unitModel=D('MisSystemUnit');
		$unitmap['status']=1;
		$unitList=$unitModel->where($unitmap)->select();
		$unitarray=array();
		foreach ($unitList as $uk=>$uVal){
			$unitarray[$uVal['danweidaima']]=$uVal['danweimingchen'];
		}
		$this->untils=$unitarray;
		/* $this->untils=array(
		'yuan'=>'元',
		'wan'=>'万元',
		'shiwang'=>'十万',
		'baiwang'=>'百万',
		'qianwang'=>'千万',
		'yi'=>'亿元',
		'pingfangmi'=>'㎡',
		'ren'	=> '人',
		'hu'	=> '户',
		'percent' => '%',
		'wfz'	=>'‰。',// 万分之一
		'qfz'	=>'‰',//千分之一
		'bfz'	=>'％', //百分之一
		'mu'	=>	'亩', // 亩
		'year'	=>	'年',
		'month'	=>	'月',

		); */
	}
	/**
	 * @Title: getDataBaseConf
	 * @Description: todo(获取数据库表、字段配置信息，返回两个数组：data=>'原始数据','cur'=>'当前action的配置数据')
	 * @author quqiang
	 * @date 2014-9-29 下午2:05:11
	 * @throws
	 */
	protected function getDataBaseConf(){
		// 配置文件信息记录
		$configData = array();
		// 从数据库中查询出表信息
		// 	formId 表单编号
		$formid = getFieldBy($this->nodeName, "actionname", "id", "mis_dynamic_form_manage");
		// 得到当前action下的所有表记录
		$misDynamicDatabaseMasObj = M('mis_dynamic_database_mas');
		$formtablelist = $misDynamicDatabaseMasObj->where('formid='.$formid)->select();
		// 得到表字段列表
		$misDynamicDatabaseSubObj = M('mis_dynamic_database_sub');
		$formfieldSql="SELECT mas.tablename , sub.* FROM mis_dynamic_database_sub AS sub LEFT JOIN mis_dynamic_database_mas AS mas ".
				"ON sub.masid = mas.id ".
				"WHERE sub.formid={$formid}";
		$formfiledList = $misDynamicDatabaseSubObj->query($formfieldSql);

		// 字段记录对象
		$fieldConfig=array();
		// 遍历字段信息将字段分解到具体表中
		foreach ($formfiledList as $fieldkey=>$fieldval){
			$fieldConfig[$fieldval['tablename']]['list'][] = array(
					'field' => $fieldval['field'],
					'isdatasouce' => $fieldval['isdatasouce'],
					'tablename' => $fieldval['tablename'],
					'type' => $fieldval['type'],
					'desc' => $fieldval['desc'],
					'length' => $fieldval['length'],
					'category' => $fieldval['category'],
					'title' => $fieldval['title'],
					'weight' => $fieldval['weight'],
					'sort' =>$fieldval['sort'],
					'isshow' => $fieldval['isshow'],
					'formid' => $fieldval['formid'],
					'masid' => $fieldval['masid'],
					'id' => $fieldval['id'],
			);
		}

		// 表记录对象
		$tableConfig=array();
		// 遍历表信息将表分解
		foreach ($formtablelist as $tablekey=>$tableval){
			$tableConfig[$tableval['tablename']] = array(
				'tablename' => $tableval['tablename'],
				'tabletitle' => $tableval['tabletitle'],
				'isprimay' => $tableval['isprimary'],
				'ischoise' => $tableval['ischoise'],
				'formid' => $tableval['formid'],
				'id' => $tableval['id'],
				'list'=>$fieldConfig[$tableval['tablename']]['list']
			);
		}
		$configData[$this->nodeName]['datebase']= $tableConfig;
		return array('data'=>$configData , 'cur'=>$configData[$this->nodeName] , 'path'=>'');
		exit;
		$model = D('Autoform');
		$dir = '/autoformconfig/';
		$dataConf = $dir.'MisAutoAnameList.inc.php';
		$model -> setPath($dataConf);
		$allDataSetConf =  require($model -> GetFile());
		$curDataSetConf = $allDataSetConf[$this->nodeName];
		return array('data'=>$allDataSetConf , 'cur'=>$curDataSetConf , 'path'=>$dataConf);
	}

	/**
	 *
	 * @Title: getTrueTableName
	 * @Description: 获取主表的真实名称，
	 * @author quqiang
	 * @date 2014-9-29 下午14:25:03
	 * @throws
	 */
	protected function getTrueTableName(){
		/*
		 $data = $this->getDataBaseConf();
		 $temp = $data['cur'];
		 foreach ($temp['datebase'] as $key => $value) {
			if($value['isprimay']){
			return $key;
			}
			}*/

		$obj = M('mis_dynamic_database_mas');
		// 	formId 表单编号
		$formid = getFieldBy($this->nodeName, "actionname", "id", "mis_dynamic_form_manage");
		$data = $obj->where("formid={$formid} AND isprimary=1")->find();
		//logs(array($formid , $this->nodeName , $this->nodeTitle , $obj->getLastSql()));
		if($data && $data['tablename']){
			return $data['tablename'];
		}else{
			$msg = "当前表单【{$this->nodeName}】没有主表设置" . $obj->getDBError() . $obj->getLastSql() ;
			throw new NullDataExcetion($msg);
			logs('C:'.__CLASS__.' L:'.__LINE__."getTrueTableName:当前表单【{$this->nodeName}】没有主表设置:sql[{$obj->getLastSql()}]".$this->pw_var_export($data),date('Y-m-d' , time()).'_data.log');
// 			$this->error("当前表单【{$this->nodeName}】没有主表设置");
		}
	}
	/**
	 * @Title: getProperty
	 * @Description: todo(获取组件的属性列表)
	 * @param string $category	组件类型
	 * @return array $temp; 组件所有属性
	 * @author quqiang
	 * @date 2014-8-21 下午12:48:01
	 * @throws
	 */
	protected function getProperty($category){
		$temp = array();
		$this->publicProperty;
		$this->privateProperty[$category];
		$temp= array_merge($this->privateProperty[$category], $this->publicProperty);
		return 	$temp;
	}

	/**
	 * 获取所有的组件属性，合并具体组件的私有属性
	 * @Title: getAllProperty
	 * @Description: todo(这里用一句话描述这个方法的作用) 
	 * @return multitype:  
	 * @author quqiang 
	 * @date 2015年4月13日 下午8:13:14 
	 * @throws
	 */
	protected function getAllProperty(){
		$tempArr=array();
		foreach ($this->privateProperty as $k=>$v){
			$tempArr = array_merge($tempArr , $v);
		}
		$temp= array_merge($tempArr, $this->publicProperty);
		return 	$temp;
	}

	/**
	 *
	 * @Title: gettableArr
	 * @Description: todo(组合数据表 及表字段信息 )
	 * @param unknown_type $key
	 * @param unknown_type $creatableList
	 * @return multitype:multitype:NULL unknown
	 * @author renling
	 * @date 2014-9-26 下午2:51:22
	 * @throws
	 */
	protected function gettableArr($key,$creatableList,$type,$datasouce=""){
		$list=array();
		$editList=array();
		if($key!=""){
			$tablename[$key]=$_POST['tablename'][$key];
		}else{
			$tablename=$_POST['tablename'];
		}
		foreach($tablename as $ckey=>$cval){
			$listArr=array();
			foreach ($_POST['fieldname'][$ckey] as $cfkey=>$cfval){
				$isdatasouce="";
				if($datasouce==$cfval){
					$isdatasouce=1;
				}else{
					$isdatasouce="";
				}
				$listArr[]=array(
						'field'=>$cfval,//字段名称
						'isdatasouce'=>$isdatasouce,//是否为数据源
						'unique'=>$_POST['fieldunique'][$ckey][$cfkey],//唯一验证
						'tablename'=>$creatableList[$ckey]['tablename'],
						'type'=>$_POST['fieldtype'][$ckey][$cfkey],//字段类型
						'desc'=>$_POST['fielddesc'][$ckey][$cfkey],//字段描述
						'length'=>$_POST['fieldlength'][$ckey][$cfkey],//字段长度
						'category'=>$_POST['fieldcategory'][$ckey][$cfkey],//组件类型
						'title'=>$_POST['fieldtitle'][$ckey][$cfkey],//字段中文名字
						'weight'=>$_POST['fieldweight'][$ckey][$cfkey],//权重
						'sort'=>$cfkey,//序号
						'isforeignfield'=>getFieldBy($_POST['oldchoiseid'][$ckey][$cfkey], "ids", "id", "mis_dynamic_form_propery"),//复用组件记录id
						'isshow'=>$_POST['fieldshow'][$ckey][$cfkey],
				);
				$list[$cfval]=array(
						'filed'=>$cfval,//字段名称
						'tablename'=>$creatableList[$ckey]['tablename'],
						'type'=>$_POST['fieldtype'][$ckey][$cfkey],//字段类型
						'unique'=>$_POST['fieldunique'][$ckey][$cfkey],//唯一验证
						'desc'=>$_POST['fielddesc'][$ckey][$cfkey],//字段描述
						'length'=>$_POST['fieldlength'][$ckey][$cfkey],//字段长度
						'category'=>$_POST['fieldcategory'][$ckey][$cfkey],//组件类型
						'title'=>$_POST['fieldtitle'][$ckey][$cfkey],//字段中文名字
						'weight'=>$_POST['fieldweight'][$ckey][$cfkey],//权重
						'isforeignfield'=>getFieldBy($_POST['oldchoiseid'][$ckey][$cfkey], "ids", "id", "mis_dynamic_form_propery"),//复用组件记录id
						'sort'=>$cfkey,//序号
				);
			}
			$editList[$creatableList[$ckey]['tablename']]=array(
					"tablename"=>$creatableList[$ckey]['tablename'],
					"tabletitle"=>$_POST['tablename'][$ckey],
					'isprimay'=>$creatableList[$ckey]['isprimary'],//是否是主表
					'ischoise'=>$creatableList[$ckey]['ischoise'],//是否为选表
					"list"=>$listArr,
			);
		}
		if($type){
			return $editList;
		}else{
			return $list;
		}
	}
	/**
	 * @Title: getAutoFormConfig
	 * @Description: 获取组件配置文件信息 ，
	 * @param string $node 配置文件节点名称，默认为index
	 * @param boolean $dbdata	是否从表中查询数据。默认false 不
	 * @author quqiang
	 * @date 2014-8-20 下午05:34:03
	 * @throws
	 * @modify nbmxkj@2011009 1054
	 */
	protected function getAutoFormConfig($node='index' , $dbdata=false){
		if(isset($node))
		$node = $this->curnode;

		if($dbdata){
			$formid = getFieldBy($this->nodeName, "actionname", "id", "mis_dynamic_form_manage");
			$data = $this->getfieldCategory($formid);
			/*
			 $sql="SELECT * FROM mis_dynamic_form_propery WHERE formid=(
				SELECT id FROM mis_dynamic_form_manage WHERE actionname='{$this->nodeName}'
				)
				AND tplid=(
				SELECT id FROM mis_dynamic_form_template WHERE formid=
				(
				SELECT id FROM mis_dynamic_form_manage WHERE actionname='{$this->nodeName}'
				) AND tplname='{$node}'
				)";
				$obj = M();
				$data = $obj->query($sql);
				$temp=null;
				if(is_array($data)){
				foreach ($data as $k=>$v){
				$v['fields'] = $v['fieldname'];
				$v['catalog']=$v['category'];
				$temp[$v['fieldname']]=$v;
				}
				*/
			return $data;
		}else{
			$dir = '/autoformconfig/';
			$path = $dir.$this->nodeName.'.php';
			/*if(!$node){
			 $path = $dir.$this->nodeName.'.php';
			 }else{
			 $path = $dir.$this->nodeName.'_'.$node.'.php';
			 } */
			$model = D('Autoform');
			$model->setPath($path);
			return $model->getOrderNode($node);
		}

	}
	/**
	 * @Title: getToolBar
	 * @Description: todo(获取页面权限)
	 * @param boolean $isaudit 是否是审批流
	 * @return array:最终页面权限信息
	 * @param int $isbaseArchives 默认：0 不启用，1：基础档案右侧为表单，新增按钮默认弹出对话框，2：基础档案右侧为表单，新增按钮ajax刷新右侧表单页面，3：基础档案右侧为列表
	 * @author quqiang
	 * @date 2014-8-21 上午10:27:08
	 * @throws
	 */
	protected  function getToolBar($isaudit = false , $isbaseArchives=0){
		/**********************************************************
		 * 生成基础档案
		 *********************************************************/
		// logs('开始');
		if($isbaseArchives){
			switch ($isbaseArchives){
				case 1:
					$temp['js-add'] = array(
						'ifcheck' => '1',
						'permisname' => strtolower($this->nodeName).'_add',
						'html' => '<a class="js-add add tml-btn tml_look_btn tml_mp" href="__URL__/add" target="dialog"  width="900" height="450" mask="true" rel="__MODULE__add"  title="'.$this->nodeTitle.'_新增"><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
						'shows' => '1',
						'sortnum' => '1',
					);
					$temp['js-delete'] = array(
						'ifcheck' => '1',
						'extendurl' => '"id/".$_REQUEST["defaultid"]',
						'permisname' => strtolower($this->nodeName).'_delete',
						'html' => '<a title="确定要删除此条数据吗?" target="ajaxTodo" href="__URL__/delete/#extendurl#/navTabId/__MODULE__" class="delete tml-btn tml_look_btn tml_mp"><span><span class="icon icon-plus icon_lrp"></span>删除</span></a>',
						'shows' => '1',
						'sortnum' => '2',
					);
					break;
				case 2:
					$temp['js-add'] = array(
						'ifcheck' => '1',
						'permisname' => strtolower($this->nodeName).'_add',
						'html' => "<a class=\"js-add add tml-btn tml_look_btn tml_mp\" href=\"__URL__/add\" target=\"ajax\" rel=\"{$this->nodeName}view\"  title=\"{$this->nodeTitle}_新增\"><span><span class=\"icon icon-plus icon_lrp\"></span>新增</span></a>",
						'shows' => '1',
						'sortnum' => '1',
					);
					$temp['js-delete'] = array(
						'ifcheck' => '1',
						'extendurl' => '"id/".$_REQUEST["defaultid"]',
						'permisname' => strtolower($this->nodeName).'_delete',
						'html' => '<a title="确定要删除此条数据吗?" target="ajaxTodo" href="__URL__/delete/#extendurl#/navTabId/__MODULE__" class="delete tml-btn tml_look_btn tml_mp"><span><span class="icon icon-plus icon_lrp"></span>删除</span></a>',
						'shows' => '1',
						'sortnum' => '2',
					);
					break;
				case 3:
					$temp['js-add'] = array(
						'ifcheck' => '1',
						'permisname' => strtolower($this->nodeName).'_add',
						'html' => '<a class="js-add add tml-btn tml_look_btn tml_mp" href="__URL__/add" target="dialog" width="740" height="600" mask="true" rel="__MODULE__add"  title="'.$this->nodeTitle.'_新增"><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
						'shows' => '1',
						'sortnum' => '1',
					);
					$temp['js-edit'] = array(
						'ifcheck' => '1',
						'rules' => '#operateid#==0',
						'permisname' => strtolower($this->nodeName).'_edit',
						'html' => '<a class="js-edit edit tml-btn tml_look_btn tml_mp"  href="__URL__/edit/id/{sid_node}" width="740" height="600" mask="true" rel="__MODULE__edit"  target="dialog" title="'.$this->nodeTitle.'_修改"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
						'shows' => '1',
						'sortnum' => '3',
					);
					$temp['js-view'] = array(
						'ifcheck' => '0',
						'permisname' => strtolower($this->nodeName).'_view',
						'html' => '<a class="js-view icon tml-btn tml_look_btn tml_mp" href="__URL__/view/id/{sid_node}" width="740" height="600" mask="true" rel="__MODULE__view" target="dialog"  title="'.$this->nodeTitle.'_查看"><span><span class="icon icon-eye-open icon_lrp"></span>查看</span></a>',
						'shows' => '0',
						'sortnum' => '4',
					);
					$temp['js-delete'] = array(
						'ifcheck' => '1',
						'permisname' => strtolower($this->nodeName).'_delete',
						'html' => '<a class="js-delete delete tml-btn tml_look_btn tml_mp" href="__URL__/delete/id/{sid_node}/navTabId/__MODULE__" target="ajaxTodo" title="你确定要删除吗？" warn="请选择一条组记录"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a>',
						'shows' => '1',
						'sortnum' => '2',
					);
					break;
				default:
					break;
			}

		}else{
			if($isaudit){
				//新增
				$temp['js-add'] = array(
					'ifcheck' => '1',
					'permisname' => strtolower($this->nodeName).'_add',
					'html' => '<a class="js-add add tml-btn tml_look_btn tml_mp" href="__URL__/add" rel="__MODULE__add" target="navTab"  title="'.$this->nodeTitle.'_新增"><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
					'shows' => '1',
					'sortnum' => '1',
				);
				//删除
				$temp['js-delete'] = array(
					'ifcheck' => '1',
					'rules' => '#auditState#==0||#auditState#==-1',
					'permisname' => strtolower($this->nodeName).'_delete',
					'html' => '<a class="js-delete delete tml-btn tml_look_btn tml_mp" href="__URL__/delete/id/{sid_node}/navTabId/__MODULE__" target="ajaxTodo" title="你确定要删除吗？" warn="请选择一条组记录"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a>',
					'shows' => '1',
					'sortnum' => '2',
				);
				//edit按钮
				$temp['js-edit'] = array(
					'ifcheck' => '1',
					'rules' => '#auditState#==0||#auditState#==-1',
					'permisname' => strtolower($this->nodeName).'_edit',
					'html' => '<a class="js-edit edit tml-btn tml_look_btn tml_mp" href="__URL__/edit/id/{sid_node}" rel="__MODULE__edit" target="navTab"  title="'.$this->nodeTitle.'_修改"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
					'shows' => '1',
					'sortnum' => '3',
				);
				$temp['js-view'] = array(
					'ifcheck' => '0',
					'permisname' => strtolower($this->nodeName).'_view',
					'html' => '<a class="js-view icon tml-btn tml_look_btn tml_mp" href="__URL__/auditView/id/{sid_node}" rel="__MODULE__view" target="navTab"   title="'.$this->nodeTitle.'_查看"><span><span class="icon icon-eye-open icon_lrp"></span>查看</span></a>',
					'shows' => '1',
					'sortnum' => '4',
				);
				//单据撤回
				$temp['js-iconBack'] = array(
					'ifcheck' => '1',
					'rules' => '#auditState#==1',
					'permisname' => strtolower($this->nodeName).'_add',
					'html' => '<a class="js-iconBack tbundo tml-btn tml_look_btn tml_mp" href="__URL__/lookupGetBackprocess/id/{sid_node}/navTabId/__MODULE__" warn="请选择节点" target="ajaxTodo" title="您确定要撤回单据吗?"><span><span class="icon icon-external-link icon_lrp"></span>单据撤回</span></a>',
					'shows' => '1',
					'sortnum' => '5',
				);
				//流程变更
				$temp['js-Change'] = array(
					'ifcheck' => '1',
					'rules' => '#auditState#==3 || #operateid#==1',
					'permisname' => strtolower($this->nodeName).'_changeedit',
					'html' => '<a class="js-Change icon tml-btn tml_look_btn tml_mp" href="__URL__/changeEdit/bgval/1/id/{sid_node}" rel="__MODULE__edit" target="navTab"   title=""'.$this->nodeTitle.'_变更"><span><span class="icon icon-eye-open icon_lrp"></span>变更</span></a>',
					'shows' => '1',
					'sortnum' => '6',
				);
				$temp['js-fileexport'] = array(
						'ifcheck' => '0',
						'permisname' => strtolower($this->nodeName).'_printout',
						'html' => '
						<a class="js-printOut tml-btn tml_look_btn tml_mp" title="导出" export_url="__URL__/fileexport" rel_id="{sid_node}" onclick="fileexport(this)" href="javascript:;" ><span class="icon_lrp"><span class="icon-sort"></span>导出</span></a>
						<div class="top_drop_lay export_operate">
							<a href="__APP__/"'.$this->nodeName.'"/export_word_one/id/" class="tml-btn tml_look_btn tml_mp export_type">
								<span class="icon icon-share icon_lrp"></span><span>导出Word</span>
							</a>
							<a href="__APP__/"'.$this->nodeName.'"/export_pdf_one/id/" class="tml-btn tml_look_btn tml_mp export_type">
								<span class="icon icon-share icon_lrp"></span><span>导出Pdf</span>
							</a>
						</div>',
						'shows' => '0',
						'sortnum' => '8',
						'rightnotshow' => '1'
				);
			}else{
				$temp['js-add'] = array(
					'ifcheck' => '1',
					'permisname' => strtolower($this->nodeName).'_add',
					'html' => '<a class="js-add add tml-btn tml_look_btn tml_mp" href="__URL__/add" target="navTab" rel="__MODULE__add"  title="'.$this->nodeTitle.'_新增"><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
					'shows' => '1',
					'sortnum' => '1',
				);
				$temp['js-edit'] = array(
					'ifcheck' => '1',
					'rules' => '#operateid#==0',
					'permisname' => strtolower($this->nodeName).'_edit',
					'html' => '<a class="js-edit edit tml-btn tml_look_btn tml_mp"  href="__URL__/edit/id/{sid_node}" rel="__MODULE__edit" target="navTab" title="'.$this->nodeTitle.'_修改"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
					'shows' => '1',
					'sortnum' => '3',
				);
				$temp['js-view'] = array(
					'ifcheck' => '0',
					'permisname' => strtolower($this->nodeName).'_view',
					'html' => '<a class="js-view icon tml-btn tml_look_btn tml_mp" href="__URL__/view/id/{sid_node}/eid/{sid_node}" rel="__MODULE__view" target="navTab"  title="'.$this->nodeTitle.'_查看"><span><span class="icon icon-eye-open icon_lrp"></span>查看</span></a>',
					'shows' => '1',
					'sortnum' => '4',
				);
				$temp['js-delete'] = array(
					'ifcheck' => '1',
					'permisname' => strtolower($this->nodeName).'_delete',
					'html' => '<a class="js-delete delete tml-btn tml_look_btn tml_mp" href="__URL__/delete/id/{sid_node}/navTabId/__MODULE__" target="ajaxTodo" title="你确定要删除吗？" warn="请选择一条组记录"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a>',
					'shows' => '1',
					'sortnum' => '2',
				);
				//流程变更
				$temp['js-Change'] = array(
						'ifcheck' => '1',
						'rules' => '#auditState#==3 || #operateid#==1',
						'permisname' => strtolower($this->nodeName).'_changeedit',
						'html' => '<a class="js-Change icon tml-btn tml_look_btn tml_mp" href="__URL__/changeEdit/bgval/1/id/{sid_node}" rel="__MODULE__edit" target="navTab"   title=""'.$this->nodeTitle.'_变更"><span><span class="icon icon-eye-open icon_lrp"></span>变更</span></a>',
						'shows' => '1',
						'sortnum' => '6',
				);
				$temp['js-fileexport'] = array(
						'ifcheck' => '0',
						'permisname' => strtolower($this->nodeName).'_printout',
						'html' => '
						<a class="js-printOut tml-btn tml_look_btn tml_mp" title="导出" rel_id="{sid_node}" export_url="__URL__/fileexport" onclick="fileexport(this)" href="javascript:;" ><span class="icon_lrp"><span class="icon-sort">导出</span></span></a>
						<div class="top_drop_lay export_operate">
							<a href="__APP__/"'.$this->nodeName.'"/export_word_one/id/" class="tml-btn tml_look_btn tml_mp export_type">
								<span class="icon icon-share icon_lrp"></span><span>导出Word</span>
							</a>
							<a href="__APP__/"'.$this->nodeName.'"/export_pdf_one/id/" class="tml-btn tml_look_btn tml_mp export_type">
								<span class="icon icon-share icon_lrp"></span><span>导出Pdf</span>
							</a>
						</div>',
						'shows' => '0',
						'sortnum' => '8',
						'rightnotshow' => '1'
				);
			}
			//加入提醒
			$temp['js-addremind'] = array(
				'ifcheck' => '0',
				'permisname' => strtolower($this->nodeName).'_remind',
				'html' => '<a class="tml_look_btn  tml_mp js-addremind" mask="true" href="__APP__/MisSystemRemind/lookupaddremind/md/'.$this->nodeName.'" rel="__MODULE__addremind" target="dialog" width="640" height="227"  title="加入提醒"><span class="icon-bell icon_lrp"></span><span>加入提醒</span></a>',
				'shows' => '1',
				'sortnum' => '5',
			);
		}
		$temp['js-printOut'] = array(
				'ifcheck' => '0',
				'permisname' => strtolower($this->nodeName).'_printout',
				'html' => '<a class="js-printOut tml-btn tml_look_btn tml_mp" title="打印" rel_id="{sid_node}" id="printOut" print_url="__URL__/printout" onclick="printOut(this,1)" href="javascript:;" ><span><span class="icon icon-edit icon_lrp"></span>打印</span></a>',
				'shows' => '0',
				'sortnum' => '7',
		);
		return $temp;
	}

	/**
	 * @Title: validateform
	 * @Description: todo(验证动态表单的必须属性值。)
	 * @author quqiang
	 * @date 2014-11-10 上午10:52:25
	 * @throws
	 */
	protected  function validateform(){
		if(!$this->nodeName){
			$this->error('Action名称未知');
		}
		if(!$this->tableName){
			$this->error('数据表名称未知');
		}
	}
	/**
	 * @Title: pw_var_export
	 * @Description: todo(用完就删除的函数)
	 * @param unknown_type $input
	 * @param unknown_type $t
	 * @author quqiang
	 * @date 2014-11-15 下午05:09:41
	 * @throws
	 */
	protected function pw_var_export($input,$t = null)
	{
		$output = '';
		if (is_array($input))
		{
			$output .= "array(\r\n";
			foreach ($input as $key => $value)
			{
				$output .= $t."\t".$this->pw_var_export($key,$t."\t").' => '.  $this->pw_var_export($value,$t."\t");
				$output .= ",\r\n";
			}
			$output .= $t.')';
		} elseif (is_string($input)) {
			$output .= "'".str_replace(array("\\","'"),array("\\\\","\'"),$input)."'";
		} elseif (is_int($input) || is_double($input)) {
			$output .= "'".(string)$input."'";
		} elseif (is_bool($input)) {
			$output .= $input ? 'true' : 'false';
		} else {
			$output .= 'NULL';
		}

		return $output;
	}
	/**
	 * @Title: lookuoconfig
	 * @Description: todo(获取lookup配置文件)
	 * @param boolean $iscur  false 程序调用，true 页面直接输出
	 * @param boolean $allcontent 是否获取所有内容
	 * @author quqiang
	 * @date 2014-11-29 下午06:46:12
	 * @throws
	 */
	function lookuoconfig($iscur=true , $allcontent=false){
		/* look up */
		$lookupMode = D('LookupObj');
		$list = $lookupMode->GetLookupDetails();
		foreach ($list as $k=>$v){
			if($allcontent){
				$temp[$k]=$v;
			}else{
				$temp[$k]=$v['title'];
			}			
		}
// 			$list = $lookupMode->GetLookupIndex();
// 			if($list){
// 				foreach($list as $k=>$v){
// 					$file = $this->GetDetailsPath.'/'.$k.'.php';
// 					if(file_exists($file)){
// 						$temp = require $file;
// 						if(!$temp['status']){
// 							unset($list[$k]);
// 						}
// 					}
// 				}
// 			}
//		}
		if($iscur){
			echo json_encode($temp);
		}else{
			return $temp;
		}
		
// 		$lookupPath = $lookupMode->GetFile();
// 		if(file_exists($lookupPath)){
// 			$lookupTemp = require($lookupPath);
// 			unset($temp);
// 			foreach ($lookupTemp as $k=>$v){
// 				if($v['status']){
// 					if($allcontent){
// 						$temp[$k]=$v;
// 					}else{
// 						$temp[$k]=$v['title'];
// 					}
// 				}
// 			}
// 		}
// 		/*  look up end */
// 		if($iscur){
// 			echo json_encode($temp);
// 		}else{
// 			return $temp;
// 		}
	}

	/**
	 * @Title: checkforconfig
	 * @Description: todo(获取checkfor配置文件)
	 * @param boolean $iscur  false 程序调用，true 页面直接输出
	 * @author quqiang
	 * @date 2014-11-29 下午06:46:38
	 * @throws
	 */
	function checkforconfig($iscur=true){
		/* look up */
		$lookupMode = D('CheckForObj');
		$lookupPath = $lookupMode->GetFile();
		if(file_exists($lookupPath)){
			$lookupTemp = require($lookupPath);
			unset($temp);
			foreach ($lookupTemp as $k=>$v){
				if($v['status']){
					$temp[$k]=$v['title'];
				}
			}
		}
		/*  look up end */
		if($iscur){
			echo json_encode($temp);
		}else{
			return $temp;
		}
	}


	/**
	 * @Title: getSelectList
	 * @Description: todo(获取seelctList)
	 * @param string $item	获取指定项
	 * @author quqiang
	 * @date 2014-11-30 下午04:55:46
	 * @throws
	 */
	protected function getSelectList($item=''){
		$model=D('Selectlist');
		if(file_exists($model->GetFile())){
			$selectlist = require $model->GetFile();
		}
		$temp = array();
		if($item){
			$temp = $selectlist[$item];
		}else{
			$temp = $selectlist;
		}
		return $temp;
	}

	/**
	 * @Title: insertField
	 * @Description: todo(往mis_dynamic_form_field有中添加字段信息)
	 * @param array $data  字段信息数据
	 * @author quqiang
	 * @date 2014-12-9 上午12:21:18
	 * @throws
	 */
	protected function insertField($data){
		$misDynamicFormFieldObj= M('mis_dynamic_form_field');
		try {
			$this->addALL('mis_dynamic_form_field',$data);
		}catch (Exception $e){
			$this->error($e->getMessage());
		}
	}
	/**
	 * @Title: addALL
	 * @Description: todo(批量添加数据)
	 * @param string	$tableName	表名
	 * @param array	 	$data  		数据,二维数组
	 * @author quqiang
	 * @date 2014-12-9 上午12:25:05
	 * @throws
	 */
	protected function addALL($tableName , $data){
		if(!$tableName || !is_array($data)){
			throw new Exception('参数错误，请查验');
			return false;
		}
		$obj=M();
		$temp = array();
		$field = array();
		$index=0;
		// 将二维数组转换为按值存放的一维数组
		foreach ($data as $k=>$v){
			if(!is_array($v)){
				throw new Exception('数据不正确');
			}else{
				$t = '';
				foreach ($v as $key=>$val){
					if($index==0){
						$field[]=$key;
					}
					$t[]="'".$val."'";
				}
				$temp[]="(".join(',', $t).")";
				$index++;
			}
		}
		if(is_array($field) && is_array($temp)){
			$fieldStr = join(',', $field);
			$dataStr = join(',', $temp);
			$sql="INSERT INTO $tableName ($fieldStr) VALUES	$dataStr";
			$ret = $obj->query($sql);
			return $ret;
		}else {
			throw new Exception('插入数据构造错误');
		}
	}
	/**
	 * 对二维数组进行排序
	 * @param $array
	 * @param $keyid 排序的键值
	 * @param $order 排序方式 'asc':升序 'desc':降序
	 * @param $type  键值类型 'number':数字 'string':字符串
	 * @example	$arr = array(array('id'=>1,'sort'=>2) , array('id'=>2,'sort'=>1));
	 * 		sort_array($arr , 'sort','desc','number');
	 */
	function sortArray(&$array, $keyid, $order = 'asc', $type = 'number') {
		if (is_array($array)) {
			foreach($array as $val) {
				$order_arr[] = $val[$keyid];
			}
			$order = ($order == 'asc') ? SORT_ASC: SORT_DESC;
			$type = ($type == 'number') ? SORT_NUMERIC: SORT_STRING;
			array_multisort($order_arr, $order, $type, $array);
		}
	}
	
	/**
	 *	通过配置文件获取指定表单的所有组件配置内容
	 * @Title: getfieldCategory
	 * @Description: todo(获取数据库中字段属性)
	 * @param int			$formid	表单ID
	 * @param boolean	$isUnsetForeignOprate	是否启用属性配置中的过滤， 默认fasle 不启用
	 * @author renling
	 * @date 2014年12月12日 下午8:52:36
	 * @throws
	 */
	public function  getfieldCategory($formid , $isUnsetForeignOprate = false){
		if(!$formid || empty($formid)){
			$msg = '表单编号未知';
			throw new NullDataExcetion($msg);
		}
		//组件属性表
		$MisDynamicFormProperyDao=M("mis_dynamic_form_propery");
		$pdMap=array();
		$pdMap['status']=1;
		$pdMap['formid']=$formid;
		$MisDynamicFormProperyList=$MisDynamicFormProperyDao->where($pdMap)->order('sort asc')->select();
		$list=array();
		foreach ($MisDynamicFormProperyList as $key=>$val){
			//获取模板名称
			$tplname=getFieldBy($val['tplid'], "id", "tplname", "mis_dynamic_form_template");
			$val['tplid'] = $tplname;
			//遍历属性列表取出当前组件类型的所有属性值 通过字段类型 匹配配置文件中组件属性
			$categorylist=$this->getProperty($val['category']);
			$tempProperty=array();
			foreach ($categorylist as $k1=>$v1){
				//$list[$v1['name']] = $val[$v1['dbfield']];
				if(true == $isUnsetForeignOprate && 1 == $v1['isforeignoprate']){
					// 配置项中的排除项直接过滤掉。
				}else{
					$tempProperty[$v1['name']]=$val[$v1['dbfield']];
				}
			}
			$list[$val['tplid']][$val['fieldname']]=$tempProperty;
		}
		return  $list;
	}
	
	/**
	 * 根据配置文件处理数据
	 * @Title: accordingConfigDealWithData
	 * @Description: todo(根据配置文件处理数据) 
	 * @param array $data  数据库中的直接查询property表出来的数据
	 * @param boolean	$isUnsetForeignOprate	是否启用属性配置中的过滤， 默认true 启用
	 * @param boolean	$formToDb		页面name 转换为数据库 filed 字段名，默认为true ， 反之则互换
	 * @author quqiang 
	 * @date 2015年4月13日 下午3:26:28 
	 * @throws
	 */
	function accordingConfigDealWithData( $data  , $isUnsetForeignOprate = true , $formToDb=true){
		if( !is_array( $data ) ){
			$msg = '数据不能解析！';
			throw new NBM($msg);
		}
		// 接收key
		$oprateReceive='dbfield';
		// 来源key
		$oprateSend = 'name';
		
		// 互换
		if(false === $formToDb){
			$oprateReceive='name';
			$oprateSend = 'dbfield';
		}
		$list='';
		foreach( $data as $key => $val ){
			$categorylist=$this->getProperty($val['category']);
			$tempProperty='';
			foreach ($categorylist as $k1=>$v1){
				if( true === $isUnsetForeignOprate && 1 == $v1['isforeignoprate'] ){
					// 配置项中的排除项直接过滤掉。
				}else{
					if($v1[$oprateReceive]){
						$tempProperty[$v1[$oprateReceive]]=$val[$v1[$oprateSend]];
					}
				}
			}
			if(is_array($tempProperty)){
				$list=$tempProperty;
			}
		}
		return $list;
	}
	
	/**
	 * 过滤允许出现差异的数据属性,返回能够直接用于数据库修改的数组结果
	 * @Title: accordingConfigDealWithData
	 * @Description: todo(根据配置文件处理数据)
	 * @param array $data  数据库中的直接查询property表出来的数据
	 * @param boolean	$isChanged		数据是否已经通用了页面name转数据库字段名 默认为true
	 * @author quqiang
	 * @date 2015年4月13日 下午3:26:28
	 * @throws
	 */
	function accordingConfigFilter( $data  , $isChanged = true){
		if( !is_array( $data ) ){
			$msg = '数据不能解析！';
			throw new NBM($msg);
		}
		// 接收key
		$oprateReceive='dbfield';
		// 来源key
		$oprateSend = 'dbfield';
		// 数据源没有通用处理时
		if(false === $isChanged){
			$oprateReceive='dbfield';
			$oprateSend = 'name';
		}
		$list='';
		foreach( $data as $key => $val ){
			$categorylist=$this->getProperty($val['category']);
			$tempProperty='';
			foreach ($categorylist as $k1=>$v1){
				if(1 == $v1['isforeignoprate'] ){
					// 配置项中的排除项直接过滤掉。
				}else{
					if($v1[$oprateReceive]){
						$tempProperty[$v1[$oprateReceive]]=$val[$v1[$oprateSend]];
					}
				}
			}
			if(is_array($tempProperty)){
				$list=$tempProperty;
			}
		}
		return $list;
	}
	
	/**
	 * @Title: getOrderNode 
	 * @Description: todo(获取指定节点的组件信息) 
	 * @param unknown_type $node  
	 * @author quqiang 
	 * @date 2014-12-15 下午02:32:52 
	 * @throws
	 */
	protected  function getOrderNode($node = 'index'){
		$formid = getFieldBy($this->nodeName, "actionname", "id", "mis_dynamic_form_manage");
		$data = $this->getfieldCategory($formid);
		return $data[$node];
	}
/**
	 * @Title: getAllControllConfig 
	 * @Description: todo(获取的所有组件的属性，) 
	 * @param int $informid	表单ID 
	 * @author quqiang 
	 * @date 2014-12-15 下午02:08:51 
	 * @throws
	 */
	protected function getAllControllConfig($informid=false){
		$formid = $informid ? $informid : getFieldBy($this->nodeName, "actionname", "id", "mis_dynamic_form_manage");
		//$curTabData = $this->getfieldCategory($formid);
		$fieldDataTemp = $this->getfieldCategory($formid);
		$fieldData = array();
		foreach($fieldDataTemp as $k=>$v){
			$fieldData = array_merge($fieldData, $v);
		}
		return $fieldData;
	}
	
	/**
	 * @Title: getJS
	 * @Description: todo(获取JS导入)
	 * @param string $path	路径
	 * @param string $type	文件名,默认为空，表示add
	 * @author quqiang
	 * @date 2014-11-14 下午03:53:24
	 * @throws
	 */
	protected function getJS($path , $type=''){
		if(!$type){
			$type = 'add';
		}
		$js='<script src="__TMPL__'.$path.'/'.$type.'.js" type="text/javascript"></script>';
		return $js;
	}
	
	/**
	 * @Title: getCss
	 * @Description: todo(获取css导入)
	 * @param string $path	路径
	 * @param string $type	文件名,默认为空，表示add
	 * @author quqiang
	 * @date 2014-11-14 下午03:53:24
	 * @throws
	 */
	
	protected function getCss($path , $type=''){
		if(!$type){
			$type = 'add';
		}
		
		$css='<link href="__TMPL__'.$path.'/'.$type.'.css" rel="stylesheet" type="text/css"/>';
		return $css;
	}

	/**
	 * 获取模板记录ID不存就新增一条
	 * @Title: selectOrInsertTplInfo
	 * @Description: todo(获取模板记录ID不存就新增一条) 
	 * @param int $formid	表单ID  
	 * @author quqiang 
	 * @date 2015年4月14日 上午10:50:08 
	 * @throws
	 */
	protected function selectOrInsertTplInfo($formid){
		$obj = M('mis_dynamic_form_template');
		$ret = getFieldBy($formid, 'formid', 'id', 'mis_dynamic_form_template');
		if(!$ret){
			$data['tplname'] = 'index';
			$data['tpltitle']	=	'默认模板';
			$data['formid']	=	$formid;
			$data['status']	=	1;
			$data['tablename']	=	$this->nodeName;
			$insertRet = $obj->add($data);
			if(false === $insertRet){
				$msg = '模板记录写入失败!'.$obj->getLastSql();
				throw  new NullDataExcetion($msg);
			}
			return $insertRet;
		}else {
			return $ret;
		}
	}
	/**
	 *
	 * @Title: createPrimaryTable
	 * @Description: todo(创建主表表结构)
	 * @param unknown $tablename
	 * @param unknown $isprimary
	 * @param unknown $fieldname
	 * @throws NullDataExcetion
	 * @author renling
	 * @date 2015年4月13日 下午6:30:51
	 * @throws
	 */
	protected function createPrimaryTable($tablename,$isprimary,$fieldname,$primaryname,&$creatableList){
		//主表key值
		$primaryKey="";
		//创建表结构字符串
		$createtable_str="";
		//字段列表集
		$fieldnameList=array();
		$MisDynamicFormManageModel=D("MisDynamicFormManage");
		//创建主表
		$ceatetable_html_s .= "\r\nCREATE TABLE IF NOT EXISTS `".$primaryname."` \r\n(`id` int(11) NOT NULL AUTO_INCREMENT  COMMENT 'ID' ,";
		$ceatetable_html_e="\r\n\t PRIMARY KEY (`id`)";
		foreach ($tablename as $k=>$v){
			if($isprimary[$k] == '1'){
				$primaryKey=$k;
				foreach ($fieldname[$k] as $pkey=>$pval){
					if(!in_array($pval,array_values($fieldnameList)) && !in_array($pval, $this->systemUserField)){
						$fieldnameList[]=$pval;
						$unique="";
						if($_POST['fieldunique'][$k][$pkey]==1){
							$unique="UNIQUE";
						}
						if($_POST['fieldtype'][$k][$pkey] == 'date'){
							$_POST['fieldtype'][$k][$pkey] = 'int';
						}
						if($_POST['fieldtype'][$k][$pkey]=="longtext"){
							$ceatetable_html.="\r\n\t`".$pval."` ".$_POST['fieldtype'][$k][$pkey]." DEFAULT NULL ".$unique." COMMENT '".$_POST['fieldtitle'][$k][$pkey]."', ";
						}else{
							$ceatetable_html.="\r\n\t`".$pval."` ".$_POST['fieldtype'][$k][$pkey]."(".$_POST['fieldlength'][$k][$pkey].")  DEFAULT NULL ".$unique." COMMENT '".$_POST['fieldtitle'][$k][$pkey]."', ";
						}
					}
				}
// 				print_r($ceatetable_html);echo end;exit;
				$creatableList[1]=array(
					'isprimary'=>1,
					'tablename'=>$primaryname,
				);
				/**
				 * 基础档案 类型字段处理，普通表单与审批表单都会生成审批字段。
				 * 	基础档案生成的字段会多一个特有的字段。
				 */
				foreach ($this->defaultField as $key=>$val){
					$ceatetable_html .="\r\n\t`".$key."` {$val['type']}({$val['length']}) ".($val['default']!=''?"DEFAULT '{$val['default']}'":"")."{$val['isnull']} {$val['desc']} ,";
				}
				foreach ($this->auditField as $key=>$val){
					$ceatetable_html .="\r\n\t`".$key."` {$val['type']}({$val['length']}) ".($val['default']!=''?"DEFAULT '{$val['default']}'":"")."{$val['isnull']} {$val['desc']} ,";
				}
				foreach ($this->baseArchivesField as $key=>$val){
					$ceatetable_html .="\r\n\t`".$key."` {$val['type']}({$val['length']}) ".($val['default']!=''?"DEFAULT '{$val['default']}'":"")."{$val['isnull']} {$val['desc']} ,";
				}
				$createtable_str=$ceatetable_html_s.$ceatetable_html.$ceatetable_html_e."\r\n)ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='".$v."'";
				//创建主表
				logs('C:'.__CLASS__.' L:'.__LINE__.'数据库建表语句 最终构成：'.$createtable_str,date('Y-m-d' , time()).'_data.log');
				$ret = $MisDynamicFormManageModel->query($createtable_str);
				$resultcere="";
				logs('C:'.__CLASS__.' L:'.__LINE__."执行结果 {$createtable_str}");
				$resultcere=$MisDynamicFormManageModel->getDbError();
				logs('C:'.__CLASS__.' L:'.__LINE__."执行结果：{$this->pw_var_export($ret)}数据库建表语句 操作结果：".$resultcere);
				if(strlen($resultcere)>5){
					$msg = "主表字段设置错误,请查证后再提交1！".$resultcere;
					logs($msg , '' ,ROOT . '/Dynamicconf/createFormLog');
					throw new NullDataExcetion($msg);
					//$this->error("主表字段设置错误,请查证后再提交1!");
				}
				unset($createtable_str);
			}
		}
	}
	/**
	 *
	 * @Title: createSubTable
	 * @Description: todo(创建子表表结构)
	 * @param unknown $tablename
	 * @param unknown $isprimary
	 * @param unknown $fieldname
	 * @param unknown $primaryname
	 * @throws NullDataExcetion
	 * @author renling
	 * @date 2015年4月13日 下午6:42:54
	 * @throws
	 */
	protected function createSubTable($tablename,$isprimary,$fieldname,$primaryname,&$creatableList){
		//创建表结构字符串
		$createtable_str="";
		//字段列表集
		$fieldnameList=array();
		$MisDynamicFormManageModel=D("MisDynamicFormManage");
		//创建子表表结构
		foreach($tablename as $key=>$val){
			//非主表
			if($isprimary[$key]!="1"){
				//获取子表名称
				$curTableName=$this->looktablename(1, $primaryname);
				$ceatechild_html_s="CREATE TABLE IF NOT EXISTS `".$curTableName."` (";
				$ceatechild_html_s.="`id` int(11) NOT NULL AUTO_INCREMENT  COMMENT 'ID'";
				$ceatechild_html_e=",PRIMARY KEY (`id`)";
				$ceatechild_html_e.=",`masid` int(11) COMMENT '关联动态表单数据ID',";
				$ceatechild_html='';
				foreach($fieldname[$key] as $ckey=>$cval){
					if(in_array($cval,array_values($fieldnameList))){
						$msg = "'".$val."'表字段'".$cval."'构建重复,请查证后再提交!";
						throw new NullDataExcetion($msg);
					}else{
						$fieldnameList[]=$cval;
						$unique="";
						if($_POST['fieldunique'][$key][$ckey]==1){
							$unique="UNIQUE";
						}
						if($_POST['fieldtype'][$key][$ckey] == 'date'){
							$_POST['fieldtype'][$key][$ckey] = 'int';
						}
						$ceatechild_html.="\r\n\t`".$cval."` ".$_POST['fieldtype'][$key][$ckey]."(".$_POST['fieldlength'][$key][$ckey].")  DEFAULT NULL  ".$unique."  COMMENT '".$_POST['fieldtitle'][$key][$ckey]."', ";
					}
				}
				/**
				 * nbmxkj@20141107 1010 子表不生成多余字段
				 */
				//foreach ($this->defaultField as $dk=>$dv){
				//	$ceatechild_html .= "\r\n\t`".$dk ."`".$dv.",";
				//}
				$ceatechild_html.="KEY `delete_{$curTableName}` (`masid`),";
				$ceatechild_html.="CONSTRAINT `delete_{$curTableName}` FOREIGN KEY (`masid`) REFERENCES `{$primaryname}` (`id`) ON DELETE CASCADE";
				// 外键删除功能 end
				$sqlStr = $ceatechild_html_s.$ceatechild_html_e.$ceatechild_html.")ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='动态表单:{$primaryname} 内嵌表 ".$primaryname."'";
				//创建子表
				$childresult=$MisDynamicFormManageModel->query($sqlStr);
				$errorStr = $MisDynamicFormManageModel->getDbError();
				if(strlen($errorStr)>5){
					$msg = "'".$val."'字段设置错误,请查证后再提交!".$MisDynamicFormManageModel->getLastSql();
					throw new NullDataExcetion($msg);
				}
				$creatableList[]=array(
						'tablename'=>$curTableName,
				);
			}
		}
	}

	/**
	 * 获取组件的配置文件
	 * @Title: getControllConf
	 * @Description: todo(获取组件具体内容的配置文件) 
	 * @param string		$category		组件类型
	 * @param string 		$type			附加类型，操作类型  
	 * @author quqiang 
	 * @date 2015年8月21日 下午4:48:41 
	 * @throws
	 */
	protected function getControllConf($category , $type='add'){
		// 根路径
		$dir = CONF_PATH;
		// 具体目录
		$oprateDir='DynamicForm/controll/';
		// 具体文件
		$path = $dir.$oprateDir.$category.'.tpl';
		// html结构
		$ret = '';
		if(is_file($path)){
				$ret = file_get_contents($path);
		}
		return $ret;
	}
	/**
	 * 获取指定页面的配置
	 * @Title: getPageConf
	 * @Description: todo(获取指定页面的配置) 
	 * @param string		$category	页面类型  
	 * @author quqiang 
	 * @date 2015年8月21日 下午4:50:16 
	 * @throws
	 */
	protected function getPageConf($category){
		// 根路径
		$dir = CONF_PATH;
		// 具体目录
		$oprateDir='DynamicForm/page/';
		// 具体文件
		$path = $dir.$oprateDir.$category.'.tpl';
		// html结构
		$ret = '';
		if(is_file($path)){
				$ret = file_get_contents($path);
		}
		return $ret;
	}

	/**
	 * 写入文件
	 * @Title: createFile
	 * @Description: todo(这里用一句话描述这个方法的作用) 
	 * @param unknown $content
	 * @param unknown $fileName
	 * @throws NullCreateOprateException  
	 * @author quqiang 
	 * @date 2015年9月15日 下午5:26:00 
	 * @throws
	 */
	protected function createFile($content , $fileName){
	    // 检查文件是否存
	    if(file_exists($fileName)){
	        // 检查写入权限
	        if (!is_writable($fileName)) {
	            chmod($fileName, 0755); // 八进制数，正确的 mode 值
	        }
	    }else{
	        // 检查或生成文件目录
	        if (! is_dir ( dirname ( $fileName ) )){
	            mk_dir ( dirname ( $fileName ), 0755 );
	        }
	    }
	    // 写入文件内容
	    if( false === file_put_contents ( $fileName, $content )){
	        $fileInfo = pathinfo($fileName , PATHINFO_BASENAME);
	        // 			$trueFileName = $fileInfo['basename'];
	        throw new NullCreateOprateException("文件{$fileInfo}写入失败");
	    }
	}
	
	/**
	 * 解析组件默认值中的动态取值
	 * @Title: propertyReplace
	 * @Description: todo(解析组件默认值中的动态取值) 
	 * @param array $socue 组件属性值中的DB项
	 * @param string $category 组件类型
	 * @return mixed  
	 * @author quqiang 
	 * @date 2015年9月28日 下午2:58:31 
	 * @throws
	 */
	protected function propertyReplace($socue , $category){
	    // 获取组件属性
	    $property = $this->privateProperty[$category];
	    foreach ($property as $key=>$val){
	        // 数据中有属性值
	       if( $socue[ $val['dbfield'] ] ){
	           $search=array();
	           $replace=array();
	           // 默认值中有动态取值的解析出来
	           if($val['default'] && preg_match_all('/#(.*?)#/', $val['default'] , $match) ){
	               // 取出需要动态赋值的key，将取得真实值。
	               foreach ($match[0] as $k=>$v){
	                   array_push($search,$v);
	                   array_push($replace, $socue[$match[1][$k]]);
	               }
	               $socue[ $val['dbfield'] ] = str_replace ( $search, $replace, $val['default'] );
	           }
	       }
	    }
	    return $socue;
	}
}
