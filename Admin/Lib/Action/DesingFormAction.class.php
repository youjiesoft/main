<?php
/**
 * 页面布局设计
 * @Title: DesingFormAction 
 * @Package package_name
 * @Description: todo(用一句话描述该类的作用) 
 * @author quqiang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015年5月8日 下午4:09:20 
 * @version V1.0
 */
class DesingFormAction extends DesingFormBaseAction {
	/**
	 * 局部操作结果
	 *
	 * @var array
	 */
	protected $_resoult;
	
	public function __construct() {
		parent::__construct ();
		$this->_resoult = array (
				'status'=>1,
				'statusCode'=>1,
				'navTabId'=>'',
				'message'=>'操作成功',
				'forward'=>'',
				'forwardUrl'=>'',
				'callbackType'=>'',
				'data'=>'',
				'checkfield'=>'',
				'refreshtabs'=>'',
				'rel'=>'',
				'redalert'=>''
				
		);
	}
	/**
	 * 数据编辑页面
	 */
	function index() {
		parent::index ();
	}
	
	/**
	 * 页面设计
	 */
	function desing() {
		try {
			// 基础配置信息
			$controlListData = $this->getControlList ();
			$this->assign ( 'controlListData', $controlListData );
			// id
			$id = intval($_GET['id']);
			if(empty($id) || !$id){
				$msg = '参数错误'.$id;
				throw new NullDataExcetion($msg);
			}
			$obj = D ( 'MisSystemDesignProperty' );
			
			// 默认选中有导航组件的第一项.
			if(!$_GET['nav']){
				unset($map);
				$map['category'] = 'navigation';
				$map['masid'] = $_GET['id'];
				$ids = $obj->where($map)->order('sort asc')->getField('id');
				if($ids){
					$_GET['nav'] = $ids;
				}
			}
			$navSouce = null;
			if($_GET['nav'] && $_GET['id']){
				
				$navid = $_GET['nav'];
				// 导航分类检索
				//$map['masid']=$id;
				$sql = "SELECT 
					  * 
					FROM
					  `mis_system_design_property` 
					WHERE 
					IF(category='panel',nav=$navid,'1=1')
					AND
					(`masid` = {$id}) 
					ORDER BY sort ASC ";
					$data = $obj->query($sql);
					
					foreach($data as $key=>$val){
						if($val['category']=='navigation'){
							$navSouce[$val['id']] = $val;
						}
					}
			}else{
				unset($map);
				$map['masid']=$id;
				$data = $obj->where($map)->order('sort asc')->select();
				echo $obj->getLastSql();
			}
			$count = $obj->max('id');
			//$count = count($data)? count($data)+1 : 1;
			$controlCheck = $this->createEditConttrol($data);
			$html =$controlCheck;// $controlCheck['control'];
			//$souce = $controlCheck['souce'];
			$this->assign ( 'data', $navSouce );
			$this->assign ( 'html', $html );
			$this->assign ( 'count', $count+1);
			$this->assign('id' , $id);
			$this->display ();
		} catch ( Exception $e ) {
			$msg = '<pre>' . $e->__toString () . '</pre>';
			$this->error ( $msg );
		}
	}
	/**
	 * 新增组件属性
	 * @Title: ajaxeditporperty
	 * @Description: todo(新增组件属性)
	 *
	 * @author quqiang
	 *         @date 2015年5月13日 上午11:24:30
	 * @throws
	 *
	 */
	function ajaxaddporperty() {
		$category = $_POST ['category'];
		$transeModel = new Model();
		$transeModel->startTrans();
		try {
			// 修改
			if ($_POST [$this->controlsConfig [$category] ['primary'] ['key']]) {
				$obj = D ( 'MisSystemDesignProperty' );
				$data = $this->parse ('',true , true);
				
				$modifyData = $obj->create ( $data );
				if (false === $modifyData) {
					$msg = $obj->getError ();
					throw new NullDataExcetion($msg);
				}
				$obj->save();
				$obj->commit();
				$controllHtml = $this->createSingleConttrol($modifyData);
				$this->_resoult ['data']['data'] = array (
						'html' => $controllHtml,
						$this->tagIndentity=>$modifyData[$this->default ['primary'] ['field']],
						'category' =>$category,
				);
				echo json_encode ( $this->_resoult );
// 				$this->success('好了' , '' , $this->_resoult ['data']);
			} else {
				$tag = $_POST[$this->tagIndentity];
				// 新增
				$data = $this->parse ('',true , true);
				$obj = D ( 'MisSystemDesignProperty' );
				$addData = $obj->create ( $data );
				
				if (false === $addData) {
					$msg = $obj->getError ();
					throw new NullDataExcetion($msg);
				}
				$addData['masid']=$_POST['masid'];
				$addret = $obj->add ( $addData );
				if (false === $ret) {
					$msg = ' 数据操作失败，'.$obj->getDBError () . ' ' . $obj->getLastSql () ;
					throw new NullDataExcetion($msg);
				}
				$addData['id'] = $addret;
				$controllHtml = $this->createSingleConttrol($addData);
				$this->_resoult ['data']['data'] = array (
						'html' => $controllHtml,
						$this->tagIndentity=>$tag,
						'category' =>$category,
				);
				$obj->commit();
				$this->_resoult['message'] = '新增数据成功';
				echo json_encode ( $this->_resoult );
			}
		} catch ( Exeption $e ) {
			$transeModel->rollback();
			$this->_resoult ['statusCode'] = 0;
			$this->_resoult ['message'] = '<pre>' . $e->__toString () . '</pre>';
			echo json_encode ( $this->_resoult );
		}
	}

	/**
	 * 重新排序后更新现有数据
	 * @Title: changeSort
	 * @Description: todo(这里用一句话描述这个方法的作用)   
	 * @author quqiang 
	 * @date 2015年5月13日 下午6:22:30 
	 * @throws
	 */
	function changeSort(){
		$sortArr = $_POST['data'];
		if($sortArr){
			$obj = new Model();
			$tablename = D ( 'MisSystemDesignProperty' )->getTableName();
			if($tablename){
				$sql = "INSERT INTO `{$tablename}` (id,sort) VALUES  {$sortArr} ON DUPLICATE KEY UPDATE sort=VALUES(sort);";
				$ret  = $obj->execute($sql);
				$obj->commit();
				echo $ret;
			}
		}
	}
	
	/**
	 * 编辑属性
	 * @Title: editproperty
	 * @Description: todo(这里用一句话描述这个方法的作用)   
	 * @author quqiang 
	 * @date 2015年5月13日 下午6:28:17 
	 * @throws
	 */
	function editproperty(){
		try {
			$id = $_GET['id'];
			if(empty($id)){
				$msg = '不能取得数据!!';
				throw new NullDataExcetion($msg);
			}
			$obj = D ( 'MisSystemDesignProperty' );
			$data = $obj->where("id={$id}")->find();
			$ret = $this->createPropertyEdit($data);
			$this->assign('vo' , $ret);
			$this->display();
		}catch (Exception $e){
			$msg = '<h1>'.$e->getMessage().'</h1><pre>' . $e->__toString () . '</pre>';
			echo ( $msg );
		}
		
	}

	function createTpl(){
		try {
			/*
			$souceContent = "AAAA<!--TEMPLATE_CONTENT_START-->BBBB<!--TEMPLATE_CONTENT_END-->CCCC";
			var_dump($souceContent);
			$content = 'FFFFFFFF';
			$souceContent = preg_replace_callback('/(\<\!--TEMPLATE_CONTENT_START--\>)+(.*?)+(\<\!--TEMPLATE_CONTENT_END--\>)/' , function($vo) use($content){
				return $vo[1].$content.$vo[3];
			} , $souceContent);
			
			var_dump($souceContent);
			exit;
			*/
			$id = $_POST['id'];
			if(empty($id)){
				$msg = '参数错误，无法生成!';
				throw new NullDataExcetion($msg);
			}
			$propertyObj = D ( 'MisSystemDesignProperty' );
			$formObj = D('DesingForm');
			$formData = $formObj->where("id={$id}")->find();
			if(!is_array($formData)){
				$msg = '文件信息获取失败!';
				throw new NullDataExcetion($msg);
			}
			$path = $formData['path'];
			if(!$path){
				$msg = '文件路径为空!';
				throw new NullDataExcetion($msg);
			}
			if(!file_exists($path)){
				$msg = '文件不存在，请重新指定！';
				throw new NullDataExcetion($msg);
			}
			
			$navDataMap['masid'] = $id;
			$navDataMap['category']='navigation';
			
			$navData = $propertyObj->where($navDataMap)->order('sort asc')->select();
			
			if(!is_array($navDataMap)){
				$msg = '配置数据获取失败!';
				throw new NullDataExcetion($msg);
			}
			
			$panelDataMap['masid'] = $id;
			$panelDataMap['category']='panel';
			if($navData[0]['id']){
				$panelDataMap['nav']=$navData[0]['id'];
			}
			$panelData = $propertyObj->where($panelDataMap)->order('sort asc')->select();
			$navContentArr = $this->createEditConttrol($navData , true);
			//$this->createEditConttrol($navData , true)['control'];
			$panelContentArr = $this->createEditConttrol($panelData , true);
			//$this->createEditConttrol($panelData , true)['control];
			// 生成正文 <!--TEMPLATE_CONTENT_START-->.*<!--TEMPLATE_CONTENT_END-->
			$content = $panelContentArr['content'];
			// 生成导航	<!--TEMPLATE_NAV_START-->.*<!--TEMPLATE_NAV_END-->
			$navigation = $navContentArr['nav'];
			$souceContent = file_get_contents($path);
			// 替换生成正文
			 $souceContent = preg_replace_callback('/(<!--TEMPLATE_CONTENT_START-->)(.*?)(<!--TEMPLATE_CONTENT_END-->)+/s' , function($vo) use($content){
				return $vo[1].$content.$vo[3];
			} , $souceContent);
			 
			 // 替换生成导航
			 	$souceContent = preg_replace_callback('/(<!--TEMPLATE_NAV_START-->)(.*?)(<!--TEMPLATE_NAV_END-->)+/s' , function($vo) use($navigation){
			 		return $vo[1].$navigation.$vo[3];
			 	} , $souceContent);
			 	
			 
			//var_dump($souceContent);
			file_put_contents($path , $souceContent);
			
			$obj_dir = new Dir;
			$directory = TMPL_PATH.C('DEFAULT_THEME').'/DesingFormTpl';
			$obj_dir->del($directory);
			
			
			$message='生成成功！';
			$this->success($message);
		}catch (Exception $e){
			$msg = '<h1>'.$e->getMessage().'</h1><pre>' . $e->__toString () . '</pre>';
			$this->error($msg);
		}
	}
	
	/**
	 * 数据源配置页面
	 * @Title: datasouce
	 * @Description: todo(这里用一句话描述这个方法的作用) 
	 * @throws NullDataExcetion  
	 * @author quqiang 
	 * @date 2015年5月15日 下午4:20:36 
	 * @throws
	 */
	function datasouce(){
		try {
			$id = $_GET['id'];
			$isEdit = $_GET['edit'];
			if(empty($id)){
				$msg = '不能取得数据!!';
				throw new NullDataExcetion($msg);
			}
			//查询面板数据
			$MisSystemDesingDatasourceModel=D("MisSystemDesingDatasource");
			$MisSystemDesingDatasourceList=$MisSystemDesingDatasourceModel->where("status=1")->getField("id,title");
			$this->assign("MisSystemDesingDatasourceList",$MisSystemDesingDatasourceList);
			if(!$isEdit){
				$settingObj = M('mis_system_design_setting');
				$settingMap['masid'] = $id;
				$vo = $settingObj->where($settingMap)->select();
				$this->assign('vo' , $vo);
				$this->assign('masid' , $id);
			}else{
				
				$ids=$_POST['id'];
				$masids=$_POST['masid'];
				$datasouceids=$_POST['datasouceid'];
				
				
				$values='';
				foreach ($ids as $k=>$v){
					$values[]= "('{$v}','{$masids[$k]}','{$datasouceids[$k]}')";
				}
				if($values){
					$obj = M();
					$fileds="`id`,`masid`,`datasouceid`";
					$value = join(',',$values);
					$sql = "INSERT INTO `mis_system_design_setting` ({$fileds}) VALUES {$value} ON DUPLICATE KEY UPDATE datasouceid=VALUES(datasouceid);";
					$ret = $obj->execute($sql);
					if(false === $ret){
						$message = $sql.'---'.$obj->getDBError();
						$this->error($message);
					}else{
						$message = '数据源修改成功!<br/><pre>'.$sql.'</pre>';
						$this->success($message);
					}
				}
			}
			$this->display();
		}catch (Exception $e){
			$msg = '<h1>'.$e->getMessage().'</h1><pre>' . $e->__toString () . '</pre>';
			echo ( $msg );
		}
	}
	
	public function autoPanelShow(){
		$key = 'nav_';
		$navid = $_GET['navid'];
		if($navid){
			$obj = D ( 'MisSystemDesignProperty' );
			$map['nav']=$navid;
			$data = $obj->where($map)->order('sort asc')->select();
			$controllCheck = $this->createEditConttrol($data , true);
			$html =$controllCheck;// $controllCheck['control'];
			
			$dir = TMPL_PATH.C('DEFAULT_THEME').'/DesingFormTpl/';
			//$dir = './Tpl/default/DesingFormTpl/';
			$path = $dir.$key.$navid.'.html';
			if(!file_exists($path)){
				file_put_contents($path, $html['content']);
			}
			
			$this->display('DesingFormTpl:'.$key.$navid);
		}
	}
	public function delete(){
		$id=$_POST['id'];
		if($id){
			$MisSystemDesignPropertyModel=M("mis_system_design_property");
			$result=$MisSystemDesignPropertyModel->where("id=".$id)->delete();
			$MisSystemDesignPropertyModel->commit();
			echo $result;
		}
	}
}