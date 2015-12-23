<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(selectlist配置文件维护) 
 * @author libo 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-7-28 下午1:47:28 
 * @version V1.0
 */
class SelectlistAction extends CommonAction{
	
	//暂时别删除
	public function abcd(){
		$model=D('Selectlist');
		$selectlist = require $model->GetFile();
		
		$model->startTrans();
		foreach($selectlist as $key=>$val){
			$data['name'] = $key;
			$data['remark'] = $val['remark'];
			$data['level'] =$val['level'];
			$data['title'] =$val['name'];
			$data['enumkey'] =implode(",", array_keys($val[$key]));
			$data['val'] =implode(",", $val[$key]);
			$data['createid'] = $_SESSION[C('USER_AUTH_KEY')];
			$data['createtime'] = time();
			$model->add($data);
		}
		//提交事务
		$model->commit();
		$this->success("xx");
	}
	
	public function index(){
		$map = $this->_search ();
		//动态配置列表项字段  包括：1、是否显示；2、是否排序；3、列宽度
		$scdmodel = D('SystemConfigDetail');
		$modelname = $this->getActionName();
		$detailList = $scdmodel->getDetail($modelname);
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		//扩展工具栏操作
		$toolbarextension = $scdmodel->getDetail($modelname,true,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		$model=D('Selectlist');
		if(file_exists($model->GetFile())){
			$selectlist = require $model->GetFile();
		}
		$i=0;
		$list=array();
		$searchField = array();
		
		
		if($map['_complex']){
			// 做全检索
			$searchField = array_keys($map['_complex']);
			array_pop($searchField);
			$searchWord = $_POST['qkeysword'];
		}else{
			// 做指定字段的检索
			$searchField = array_keys($map);
			$t = explode('.',$searchField[0]);
			if(count($t>1)){
				$searchWord = $_POST['quick'.$t[1]];
			}else{
				$searchWord = $_POST['quick'.$t[0]];
			}
			
		}
		// 1: 先得到用户指定的查询条件。 查询字段 及 对应的 值。
		// 2: 在源数据遍历的时候 比较用户指定 字段 与 值的 关系 符合的要求的写入临时 数组
		$name=$_POST['name'][1];
		//dump($map);
		foreach ($selectlist as $k=>$v){
			$v['sort']=$i;
			$v['data']=implode(",", $v[$k]);
			$v['title']=$v['name'];
			$v['name']=$k;
			if($searchWord){
				//构建判断条件
				$cont2 = array();
				foreach($searchField as $key=>$val){
					$cont = explode('.',$val);
					if(count($cont)>1){
						$cont1 = $cont[1];
					}else{
						$cont1 = $cont[0];
					}
					$cont2[] = strpos($v[$cont1],$searchWord) !== false;
				}
				$check = 0;//没找到为0
				foreach($cont2 as $key=>$val){
					if($val){
						$check = 1;
					}
				}
				if($check){
					$list[][$k]=$v;
				}
			}else{
				$list[][$k]=$v;
			}
			$i++;
		}
// 		$data[$i]['sort']=$i;
// 		$data[$i]['name']=$k;
// 		$data[$i]['data']=implode(",", $v[$k]);
// 		$curData[] = reset($data[$i]);
	if($_POST['pageNum']){
		$pageNum=$_POST['pageNum'];
	}else{
		$pageNum=1;
	}
		$this->getPager($list,$pageNum,C("PAGE_LISTROWS"));
// 		$this->assign("list",$selectlist);
		$this->display();
	}
	/**
	 * (non-PHPdoc)新增类型
	 * @see CommonAction::insert()
	 */
	public function insert() {
		$model=D('Selectlist');
		$name = $_POST['name'];
		$vo = $model->GetRules($name);
		if($vo){
			$this->error("输入的名称有重名，请重新输入！");
		} else {
			$arr=array();
			if($_POST['arr_key']){
				foreach ($_POST['arr_key'] as $k=>$v){
					$arr[$v]= $_POST['arr_val'][$k];
				}
			}
			$data = array(
					'remark'=>$_POST['remark'],
					'status'=>$_POST['status'],
					'name' => $name, 
					'title' =>$_POST['title'],
					'level' => 15, // 屈强@2014-08-07 新增当前项权限
					'enumkey'=>implode(",", array_keys($arr)),
					'val'=>implode(",",$arr),
					'createid'=>$_SESSION[C('USER_AUTH_KEY')],
					'createtime'=>time(),
					'conftype'=>$_POST['conftype'],//配置类型 0用户自定义 1系统配置 默认用户自定义 谢友志 2015-09-16
			);
			$result = $model->add($data);
			if($result){
				$selectlist = $model->where('status=1')->select();
				$selectinc = array();
				foreach($selectlist as $key=>$val){
					$enumval = explode(",", $val['val']);
					$enumkey = explode(",", $val['enumkey']);
					$enumArr = array();
					foreach($enumval as $ek=>$ev){
						$enumArr[$enumkey[$ek]] = $ev;
					}
					
					$selectinc[$val['name']]=array(
							'remark'=>$val['remark'],
							'status'=>$val['status'],
							'name' => $val['title'], // 屈强@2014-08-07 新增标题项
							'conftype'=>$_POST['conftype'], //配置类型 0用户自定义 1系统配置 默认用户自定义 谢友志 2015-09-16
							'level' => 15, // 屈强@2014-08-07 新增当前项权限
							$val['name']=>$enumArr,
					);
				}
				$model->SetRules($selectinc);
				$this->success("操作成功");
			}else{
				$this->error("操作失败");
			}
			
		}
	}
	/**
	 * (non-PHPdoc)编辑
	 * @see CommonAction::edit()
	 */
	public function edit(){
		$model=D('Selectlist');
		if(file_exists($model->GetFile())){
			$selectlist = require $model->GetFile();
		}
		$this->assign("name",$_REQUEST['id']);
		$this->assign("vo",$selectlist[$_REQUEST['id']]);
		$this->assign("selectlistname",$selectlist[$_REQUEST['id']][$_REQUEST['id']]);
// 		dump($selectlist[$_REQUEST['id']]);
		$this->display();
	}
	public function view(){
		$this->edit();
	}
	public function update() {
		$model=D('Selectlist');
		$id = $_POST['id'];
		$name = $_POST['name'];
		if($id != $name){
			//验证是否修改了名称
			$where['name'] = array('eq',$name);
			$bool = $model->where($where)->count();
			if($bool){
				$this->error("输入的名称有重名，请重新输入！");
				exit;
			}
		}
		
		if(file_exists($model->GetFile())){
			$selectlist = require $model->GetFile();
		}
		$arr=array();
		if($_POST['arr_key']){
			foreach ($_POST['arr_key'] as $k=>$v){
				$arr[$v]= $_POST['arr_val'][$k];
			}
		}
		$data=array(
				'name' =>$name,
				'title' =>$_POST['title'],
				'remark'=>$_POST['remark'],
				'status'=>$_POST['status'],
				'enumkey'=>implode(",", array_keys($arr)),
				'val'=>implode(",", $arr),
				'updateid'=>$_SESSION[C('USER_AUTH_KEY')],
				'updatetime'=>time(),
				'conftype'=>$_POST['conftype'],//配置类型 0用户自定义 1系统配置 默认用户自定义 谢友志 2015-09-16
		);
		$where['name'] = array("eq",$id);
		$result = $model->where($where)->save($data);
		if($result===false){
			$this->error("操作失败");
		}else{
			$selectlist = $model->where ( 'status=1' )->select ();
			$selectinc = array ();
			foreach ( $selectlist as $key => $val ) {
			
				$enumval = explode(",", $val['val']);
				$enumkey = explode(",", $val['enumkey']);
				$enumArr = array();
				foreach($enumval as $ek=>$ev){
					$enumArr[$enumkey[$ek]] = $ev;
				}
					
				$selectinc[$val['name']]=array(
						'remark'=>$val['remark'],
						'status'=>$val['status'],
						'name' => $val['title'], // 屈强@2014-08-07 新增标题项
						'conftype'=>$_POST['conftype'],//配置类型 0用户自定义 1系统配置 默认用户自定义 谢友志 2015-09-16
						'level' => 15, // 屈强@2014-08-07 新增当前项权限
						$val['name']=>$enumArr,
				);
			}
			$model->SetRules ( $selectinc );
			$this->success ( "操作成功" );
		}
			
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
		$start = ($index-1)*$count;
		$end = $index * $count;
		$curData=array();
		// 获取当前数据
		for($i =$start ; $i<count($data);$i++){
			if($i < $end){
				$curData[key($data[$i])] = reset($data[$i]);
			}
		}
		//给每条数据分配该有的toolbar操作按钮
		$this->setToolBorInVolist($curData);
		$this->assign("totalCount",count($data));
		$this->assign("currentPage",$index);
		$this->assign("numPerPage",$count);
		$this->assign("list",$curData);
	}
	public function initSelectList(){
		$model = D($this->getActionName());
		$list = $model->select();
		$selectinc = array();
		foreach($list as $key=>$val){
			$opkey = explode(",",$val['enumkey']);
			$opval = explode(",",$val['val']);
			$keyval = array();
			foreach($opkey as $k=>$v){
				$keyval[$v] = $opval[$k];
			}
			$selectinc[$val['name']]=array(
					'remark'=>$val['remark'],
					'status'=>$val['status'],
					'name' => $val['title'], // 屈强@2014-08-07 新增标题项
					'conftype'=>$val['conftype']?$val['conftype']:0,//配置类型 0用户自定义 1系统配置 默认用户自定义 谢友志 2015-09-16
					'level' => 15, // 屈强@2014-08-07 新增当前项权限
					$val['name']=>$keyval,
			);
		}
		$model->SetRules ( $selectinc );
		$json = '{"status":1,"statusCode":1,"navTabId":Selectlist,"message":"\u64cd\u4f5c\u6210\u529f","forward":null,"forwardUrl":null,"callbackType":null,"data":"","checkfield":null,"refreshtabs":null,"rel":null,"redalert":0}';
		echo '<script>navTabAjaxDone('.$json.')</script>';
		exit;
	}
}

?>