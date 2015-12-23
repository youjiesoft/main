
<?php
/**
 * @Title: file_name
 * @Package package_name
 * @Description: todo(知识库和专家问答)
 * @author yuansl
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-12-31 下午3:59:53
 * @version V1.0
 */
class MisSystemNoticeMethodAction extends Action{
	function _initialize(){
		$this->transaction_model=M();
		$this->transaction_model->startTrans();
		$this->getcompanylogo();
	}
	/**
	 * @Title: getcompanylogo
	 * @Description: todo(获取公司logo)
	 * @param unknown_type $companyid
	 * @author yuansl
	 * @date 2014-6-19 下午4:05:30
	 * @throws
	 */
	private function getcompanylogo($companyid){
		$model = D("MisSystemCompany");
		if($companyid){
			$map['id'] = $companyid;
		}
		$map['status'] = array('eq',1);
		$Copany_Info = $model->where($map)->find();
		$this->assign("Copany_Info",$Copany_Info);
	}
	/**
	 * @Title: knowledge
	 * @Description: todo(知识库主页)
	 * @author yuansl
	 * par     $NUMB  配置首页每个每块中显示的文章条数
	 * par     $BLOCK_NUM  配置显示首页分类模块个数
	 * @date 2014-1-10 下午5:05:43
	 * @throws
	 */
	public function knowledge(){
		$NUMB = 10 ;
		$BLOCK = 10 ;
		//查询公司网站
		$model=D("MisSystemCompany");
		$url=$model->where("status=1")->getField('website');
		$this->assign('url',$url);
		//搜索条件
		$keywords = $_REQUEST['keywords'];
		if($keywordes){
			$topMap['_string'] = "(mis_knowledge_list.title like "."'%".$keywords."%'".")  OR ( mis_knowledge_list.content like "."'%".$keywords."%'".") ";
		}
		$MisKnowledgeTypeViewMode = D('MisKnowledgeTypeView');
		$topMap['mis_knowledge_list.status'] = array('eq',1);
		$topMap['mis_knowledge_list.type'] = array('eq','Q');
		$topMap['mis_knowledge_list.auditState'] =array('eq',3);
		$topMap['mis_knowledge_type.status'] = array('eq',1);
		$mistopList=$MisKnowledgeTypeViewMode->where($topMap)->group("parentid")->getField("parentid,name");//parentid=>name 返回值类型
// 		//限定个数
// 		$block_num = count($mistopList);
// 		$i = 0;
// 		$newtopList = array();
// 		if($block_num >$BLOCK){
// 			foreach($mistopList as $val_bloc){
// 				$i++;
// 				 if($i > $BLOCK){
// 				 	break;
// 				 }
// 				 array_push($newtopList, $val_bloc);
// 			}
// 		}
// 		$mistopList = $newtopList;
// // 		dump($mistopList);
		$listarray = array();
		foreach ($mistopList as $key=>$val){
			$topMap['parentid'] = $key;
			$listarray[$key]['obj'] = array('id'=>$key,'name'=>$val);
			$listarray[$key]['list'] = $MisKnowledgeTypeViewMode->where($topMap)->order('createtime desc')->limit($NUMB)->order('createtime desc')->select();//通过
		}
		$this->assign('listarray',$listarray);
		$this->hotlist();//热点关注
		$this->banner();
		$this->display();
	}
	/**
	 * @Title: hotlist 
	 * @Description: todo(本模块公用方法,读取热点数据)   
	 * @author yuansl 
	 * par     $NUM   配置显示热点数据条数
	 * @date 2014-2-12 下午3:18:25 
	 * @throws
	 */
	public function hotlist(){
		$NUM = 10 ;
		$MisKnowledgeListModel= D('MisKnowledgeList');
		$condition['status'] = array('eq',1);
		$condition['type'] = array('eq','Q');
		$condition['auditState'] = array('eq',3);
		$hotList = $MisKnowledgeListModel->where($condition)->order('count desc')->limit($NUM)->select();
		$newhotList = array();
		foreach($hotList as $vcontl){
			$str = htmlspecialchars_decode($vcontl['title'], ENT_QUOTES);
			$str = trim(strip_tags(str_replace("&nbsp;", ' ', $str)));
			$vcontl['title'] =  mb_substr($str, 0, 14, 'utf-8');
			array_push($newhotList, $vcontl);
		}
		$this->assign('hotlist',$newhotList);
	}
	/**
	 * @Title: knowledgeson 
	 * @Description: todo(根据一个分类id获取该分类下的文章)   
	 * @author yuansl 
	 * @date 2014-2-12 下午5:11:49 
	 * @throws
	 */
	public function knowledgeson(){
		//这是一个顶级分类的id
		$topid = $_REQUEST['id'];
		//根据得到的顶级分类得到他下面的子级分类
		$MisKnowledgeTypeModel= D('MisKnowledgeType');
		$position = $MisKnowledgeTypeModel->where("id = ".$topid)->field('name')->find();
		$this->assign('position',$position['name']);
		$sontypelist = $MisKnowledgeTypeModel->where("status = 1 and parentid = ".$topid)->field('id')->select();//子分类列表
		$MisKnowledgeListModel= D('MisKnowledgeList');
		//得到从页面穿过来的顶级分类的id值
		$sonlist = array();
		foreach($sontypelist as $vs){
			$arra = $MisKnowledgeListModel->where('status = 1 and categoryid ='.$vs['id'])->select();//field('id')->
			//除去没有文章的子分类
			if(count($arra)>0){
				array_push($sonlist, $vs['id']);
			}
		}
		//子类的纯id集合
		$lisarray = array();
		foreach($sonlist as $vk){
			$lisarray[$vk]['obj'] = array('typeid'=>$vk);//用来存当前分类的id
			$lisarray[$vk]['list'] = $MisKnowledgeListModel->where('status = 1 and categoryid ='.$vk)->limit(20)->order('createtime desc')->select();//用来存当前子类下的文章记录
		}
		$this->assign('lisarray',$lisarray);
		$this->hotlist();
		$this->display();
	}
	
	/**
	 * @Title: _before_add 
	 * @Description: todo(发表文章前缀操作)   
	 * @author yuansl 
	 * @date 2014-2-17 下午4:31:07 
	 * @throws
	 */
	public function _before_knowledgepublish(){
		if($this->islogin() == false){
			$this->redirect('Public/login','' , 0);
		}
		$MisKnowledgeTypeModel   =D("MisKnowledgeType");
		//得到顶级分类
		$topTypeList = $MisKnowledgeTypeModel->where("status=1 and parentid = 0")->field('id,name')->select();
		//过滤除去没有子类的顶级分类
		$lssary = array();
		foreach($topTypeList as $vat){
			$counts = $MisKnowledgeTypeModel->where("status = 1 and parentid= ".$vat['id'])->count();
			if($counts > 0){
				array_push($lssary, $vat);
			}
		}
		$this->assign('topTypeList',$lssary);
		//默认子级下拉框分类
		$topid = $lssary[0][id];
		$sontypelist = $MisKnowledgeTypeModel->where("status = 1 and parentid = ".$topid)->field('id,name')->select();
		$this->assign('sontypelist',$sontypelist);
		//订单号可写
		$scnmodel = D('SystemConfigNumber');
		$orderno = $scnmodel->GetRulesNO('mis_knowledge_list');
		$this->assign("orderno", $orderno);
		$this->assign("upload_path", date("Y/m/d",time())."/".$_SESSION[C('USER_AUTH_KEY')]);
	}
	/**
	 * @Title: knowledgepublish 
	 * @Description: todo(当前用户,发表文章)   
	 * @author yuansl 
	 * @date 2014-2-17 下午2:08:09 
	 * @throws
	 */
	public function knowledgepublish(){
		if($this->islogin() == false){
			$this->redirect('Public/login','' , 0);
		}
		//PUBLIC_PATH,PUBLIC绝对路径
		$ppath = str_replace('\\', "/", PUBLIC_PATH);
		$this->assign('pubpath',$ppath);
		$this->display();
	}
	/**
	 * @Title: lookupinsertkon 
	 * @Description: todo(处理用户提交文章数据)   
	 * @author yuansl 
	 * @date 2014-2-20 下午5:13:12 
	 * @throws
	 */
	public function lookupinsertkon(){
		if($this->islogin() == false){
			$this->redirect('Public/login','' , 0);
		}
		$MisKnowledgeListModel= D('MisKnowledgeList');
		//订单号可写
		$scnmodel = D('SystemConfigNumber');
		$orderno = $scnmodel->GetRulesNO('mis_knowledge_list');
		$data['orderno'] = $orderno;
		$data['orderno'] = $_POST['orderno'];
		$data['createid'] = $_SESSION[C('USER_AUTH_KEY')];
		$data['createtime'] = time();
		$data['categoryid'] = $_POST['categoryid'];
		$data['title'] = $_POST['title'];
		$data['content'] = $_POST['content'];
		$data['type'] = "Q";
		$re = $MisKnowledgeListModel->add($data);
		if($re){
			//附件保存
			$this->insertkon_swf($re);
			//启动流程
			$_POST['id']=$re;
			$_POST['dotype']='流程新建';
			A('MisKnowledgeManage')->startprocess();
			$MisKnowledgeListModel->commit();
			header("content-type:text/html;charset=utf-8");
			$this->redirect('MisSystemNoticeMethod/knowledge','' , 2,'提交成功,等待审核!');
		}else{
			$this->error("发表失败,稍后重试!");
		}
		
	}
	public function banner(){
		$NUMB = 10 ;
		$BLOCK = 10 ;
		$MisKnowledgeTypeViewMode = D('MisKnowledgeTypeView');
		$topMap['mis_knowledge_list.status'] = array('eq',1);
		$topMap['mis_knowledge_list.type'] = array('eq','Q');
		$topMap['mis_knowledge_list.auditState'] =array('eq',3);
		$topMap['mis_knowledge_type.status'] = array('eq',1);
		$mistopList=$MisKnowledgeTypeViewMode->where($topMap)->group("parentid")->getField("parentid,name");//parentid=>name 返回值类型
		$listarray = array();
		foreach ($mistopList as $key=>$val){
			$topMap['parentid'] = $key;
			$listarray[$key]['obj'] = array('id'=>$key,'name'=>$val);
			$listarray[$key]['list'] = $MisKnowledgeTypeViewMode->where($topMap)->order('createtime desc')->limit($NUMB)->order('createtime desc')->select();//通过
		}
		//在一级下并入所有二级
		$newlis = array();
		$sonlistarr = array();
		$temparr = array();
		$MisKnowledgeTypeModel = D("MisKnowledgeType");
		$MisKnowledgeListModle = D("MisKnowledgeList");
		foreach($listarray as $casx){
			$casx['sontypelist'] = $MisKnowledgeTypeModel->where("status = 1 and parentid = ".$casx['obj']['id'])->select();
			array_push($newlis, $casx);
		}
		$this->assign('listarray',$newlis);
	}
	/**
	 * @Title: insertkon_swf 
	 * @Description: todo(附件上传) 
	 * @param unknown_type $insertid  
	 * @author yuansl 
	 * @date 2014-6-20 下午4:24:37 
	 * @throws
	 */
	public  function insertkon_swf($insertid){
			$this->swf_upload($insertid,33);
	}
	/**
	 * 创建目录 2012-12-17
	 * @author wangcheng
	 * @param string $path 创建目录路径
	 */
	function createFolders($path) {
		if (!file_exists($path)) {
			$this->createFolders(dirname($path));
			mkdir($path, 0777);
		}
	}
	//上传处理
	public function swf_upload($insertid,$type,$subid=0,$m=""){
		$save_file=$_POST['swf_upload_save_name'];
		$source_file=$_POST['swf_upload_source_name'];
		$attModel = D("MisAttachedRecord");
		//临时文件夹里面的文件转移到目标文件夹
		foreach($save_file as $k=>$v){
			if($v){
				$fileinfo=pathinfo($v);
				$from = UPLOAD_PATH_TEMP.$v;//临时存放文件
				if( file_exists($from) ){
					$p=UPLOAD_PATH.$fileinfo['dirname'];// 目标文件夹
					if( !file_exists($p) ) $this->createFolders($p); //判断目标文件夹是否存在
					$to = UPLOAD_PATH.$v;
					rename($from,$to);
					//保存附件信息
					$data=array();
					$data['type']=$type;
					$data['orderid']=$insertid;
					$data['tablename'] = $m ? $m:$this->getActionName();
					$data['tableid']=$insertid;
					$data['subid']=$subid;
					$data['attached']= $v;
					$data['upname']=$source_file[$k];
					$data['createtime'] = time();
					$data['createid'] = $_SESSION[C('USER_AUTH_KEY')]?$_SESSION[C('USER_AUTH_KEY')]:0;
					$rel=$attModel->add($data);
					if(!$rel){
						$this->error( L('_ERROR_') );
					}
				}
			}
		}
	}
	/**
	 * @Title: lookupgetsontypelist
	 * @Description: todo(获取文章分类,ajax请求)
	 * @author yuansl
	 * @date 2014-2-17 下午4:38:43
	 * @throws
	 */
	public function lookupgetsontypelist(){
		$topid = $_REQUEST['bepart'];
		$MisKnowledgeTypeModel = M("MisKnowledgeType");
		$sontypelist = $MisKnowledgeTypeModel->where("status = 1 and parentid = ".$topid)->field('id,name')->select();
		echo  json_encode($sontypelist);
	}
	
	/**
	 * @Title: addpublish 
	 * @Description: todo(处理用户提交的文章数据)   
	 * @author yuansl 
	 * @date 2014-2-17 下午2:29:13 
	 * @throws
	 */
// 	public function addpublish(){
// 		$MisKnowledgeListModel= D('MisKnowledgeList');
// 		$data['createtime'] = array('eq',time());
// 		$data['createid'] = $_SESSION[C('USER_AUTH_KEY')];
// 		$data['title'] = $_REQUEST['title'];
// 		$data['type'] = "Q";
// 		$data['content'] = htmlspecialchars($_REQUEST['content']);
// 		$data['categoryid'] = $_REQUEST['categoryid'];
// 		$re = $MisKnowledgeListModel->add($data);
// 		if($re){
// 			$this->success("文正发表成功,请耐心等待审核结果!");
// 		}else{
// 			$this->error("文章发表失败,请稍后重试!");
// 		}
// 	}
	/**
	 * @Title: knowledgedetail 
	 * @Description: todo(这条文章的详细)   
	 * @author yuansl 
	 * @date 2014-2-13 下午12:51:48 
	 * @throws
	 */
	public function knowledgedetail(){
		$id = $_REQUEST['id'];
		//查询公司网站
		$model=D("MisSystemCompany");
		$url=$model->where("status=1")->getField('website');
		$this->assign('url',$url);
		//得到赞和踩的总数,并传到页面上
		$this->getagree($id);
	//当前位置
		$MisKnowledgeListModel = D('MisKnowledgeList');
		$cate =$MisKnowledgeListModel->where('status = 1 and id ='.$id)->field('id,categoryid')->find();
   //	echo $MisKnowledgeListModel->getLastSql();
		$categoryid = $cate['categoryid'];//子分类的id
		$MisKnowledgeTypeModel= D('MisKnowledgeType');//取出这个文章所属的分类
		$typeArr = $MisKnowledgeTypeModel->where("status = 1 and id =".$categoryid)->field('name,parentid')->find();
		//得到顶级分类名称
		$topTypArr = $MisKnowledgeTypeModel->where("status = 1 and id =".$typeArr['parentid'])->field('id,name')->find();
// 		$toptypeName = $topTypArr['name'];
		$this->assign('topTypeArr',$topTypArr);
	//该条文章
		$MisKnowledgeListModel= D('MisKnowledgeList');
		$arc = $MisKnowledgeListModel->where("status = 1 and type = 'Q' and id = ".$id." and closedbyid = 0")->find();
		$arc['content'] = htmlspecialchars_decode($arc['content']);
		//点击数
		$count=$arc['count']+1;
		$data['count'] = $count;
		$data['id'] = $id;
		$MisKnowledgeListModel->save($data);
// 		$this->transaction_model->commit();//事务提交
		$MisKnowledgeListModel->commit();
		$str = htmlspecialchars_decode($arc['content'], ENT_QUOTES);
		$str = trim(strip_tags(str_replace("&nbsp;", ' ', $str)));
		//修改字符截取 从0开始
		$arc['content_ful'] =  mb_substr($str, 0, 50, 'utf-8')."...";
		$this->assign('arc',$arc);
		//上一页
			$mapl['closedbyid'] = array("eq",0);
			$mapl['id'] = array("gt",$id);
			$mapl['status'] = array('eq',1);
			$mapl['type'] = array('eq','Q');
			$mapl['categoryid'] = array('eq',$categoryid);
// 			$updataid = $MisKnowledgeListModel->where($mapl)->order('createtime desc')->getField('id');
			$updataid = $MisKnowledgeListModel->where($mapl)->getField('id');
			$this->assign("updata",$updataid);
		//下一页
			$mapl['closedbyid'] = array("eq",0);
			$mapg['id'] = array("lt",$_REQUEST['id']);
			$mapg['status'] = array('eq',1);
			$mapg['type'] = array('eq','Q');
			$mapg['categoryid'] = array('eq',$categoryid);
			$downdataid = $MisKnowledgeListModel->where($mapg)->order('createtime desc')->getField('id');
			$this->assign("downdata",$downdataid);
		$this->assign('sontypeName',$typeName);
		//栏目
		$this->banner();
		//$_REQUEST['id'] 根据当前文章的额id,获取它顶级分类的id
		//热点关注
		$this->hotlist();
		//附件操作
		$MisAttachedRecordModel=M('MisAttachedRecord');
		//获取所有附件
		$condition['type'] = 33;
		$condition['tableid'] = $arc['id'];
		$condition['tablename'] = 'MisKnowledgeManage';
		$attachedarr=$MisAttachedRecordModel->where($condition)->select();
		if($attachedarr){
			$this->assign('attarry',$attachedarr);
		}
		$this->checkattrR($_REQUEST['id']);
		$this->display();
	}
	
	/**
	 * @Title: checkattrR
	 * @Description: todo(检查用户是否具有下载附件权限)
	 * @param string $id
	 * @author yuansl
	 * @date 2014-7-2 上午9:33:27
	 * @throws
	 */
	private function checkattrR($id){
			$modelV = D("MisKnowledgeLevelsVisibility");
			$modelArcti = D("MisKnowledgeList");
			$userModel = D("User");
			if($id){
				$arcticl = $modelArcti->where(" status = 1 and id = ".$id)->find();// and levels = ".$levels
				$arcticl['levels'];
				$attrR = $modelV->where("status = 1 and levels = ".$arcticl['levels'])->find();
// 				echo $modelV->getLastSql();
				if($attrR['dutyid']){
					$map['duty_id'] = array('in',explode(',',$attrR['dutyid']));
				}
				if($attrR['deptid']){
					$map['dept_id'] =  array('in',explode(',',$attrR['deptid']));
				}
				$map['status'] = array('eq',1);
				$LegalUserList = $userModel->where($map)->getField("id",true);
				if(in_array($_SESSION[C('USER_AUTH_KEY')],$LegalUserList)){
					$this->assign("attR_Right",1);
				}else{
					$this->assign("attR_Right",0);
				}
			}
		}
	/**
	 * @Title: getagree
	 * @Description: todo(拿到点赞数)
	 * @param unknown_type $arcid
	 * @author yuansl
	 * @date 2014-1-18 上午11:45:30
	 * @throws
	 */
	public function getagree($arcid){
		$MisKnowledgeCountModel = D('MisKnowledgeCount');
		$agree = $MisKnowledgeCountModel->where('status = 1 and arcticleid = '.$arcid.' and agree = 1')->count();//得到赞的总数
		$noagree = $MisKnowledgeCountModel->where('status = 1 and arcticleid = '.$arcid.' and agree = 0')->count();//得到踩的总数
		$this->assign('agree',$agree);
		$this->assign('noagree',$noagree);
		//赞踩 所占百分比颜色条 计算 百分比值
		$agreepercent = 100 * $agree/($agree +$noagree)."%";
		$noagreepercent = 100 * $noagree/($agree +$noagree)."%";
		if($agree == 0){
			$agreepercent = '0%';
		}
		if($noagree == 0){
			$noagreepercent = '0%';
		}
		$this->assign('agreepercent',$agreepercent);
		$this->assign('noagreepercent',$noagreepercent);
	}
	/**
	 * @Title: knowledgelist 
	 * @Description: todo("某个分类下"的文章列表)   
	 * par 
	 * @author yuansl 
	 * @date 2014-2-13 上午9:39:44 
	 * @throws
	 */
	public function knowledgelist(){
		$NUMLIST = 8 ;
		//查询公司网站
		$model=D("MisSystemCompany");
		$url=$model->where("status=1")->getField('website');
		$this->assign('url',$url);
		$typeid = $_REQUEST['typeid'];//顶级分类的id
		//当前位置,顶级分类id,传过去,使用getfieldby
		$this->assign('typeid',$typeid);
		//得到该分类下的子分类
		$MisKnowledgeTypeViewMode = D('MisKnowledgeTypeView');//子类和文章关联起来之后,然后文章的父id就是顶级分类的id
		$topMap['mis_knowledge_type.parentid'] = array('eq',$typeid);
		$topMap['mis_knowledge_list.status'] = array('eq',1);
		$topMap['mis_knowledge_list.closedbyid'] = array('eq',0);
		$topMap['mis_knowledge_list.type'] = array('eq','Q');
		$topMap['mis_knowledge_list.auditState'] = array('eq',3);
		$topMap['mis_knowledge_type.status'] = array('eq',1);
		$SonTypeList=$MisKnowledgeTypeViewMode->where($topMap)->field('id,name')->group("id")->select();//得到子分类列表
// 		echo $MisKnowledgeTypeViewMode->getLastSql();
// 		dump($SonTypeList);
		//作为栏目去显示
		$this->assign('SonTypeList',$SonTypeList);
		//得到传子分类的id,根据真个id去查找该分类下的文章
		if($_REQUEST['id']==null){
			$id =$SonTypeList[0]['id'];//默认,从子类列表中选取第一个子类的id
		}else{
			$id =$_REQUEST['id'];//超链接级子类的id
		}
		$this->assign('id',$id);
		$sonArc['mis_knowledge_list.status']= array('eq',1);
		$sonArc['mis_knowledge_list.closedbyid']= array('eq',0);
		$sonArc['mis_knowledge_list.type'] = array('eq','Q');
		$sonArc['mis_knowledge_list.categoryid'] = array('eq',$id);
		$sonArc['mis_knowledge_type.status'] = array('eq',1);
		//分页
		import('ORG.Util.Page');
		$count  = $MisKnowledgeTypeViewMode->where($sonArc)->count();
		//echo $MisKnowledgeListModel->getLastSql();
		$page   = new Page($count,$NUMLIST);//默认显示3条数据 
		foreach($sonArc as $key=>$val) {
			$Page->parameter   .=   "$key=".urlencode($val)."&";
		}
		$show   = $page->show();
		$list = $MisKnowledgeTypeViewMode->where($sonArc)->limit($page->firstRow.','.$page->listRows)->order("createtime desc")->select();
		$newlist = array();
		foreach($list as $vcontl){
			$str = htmlspecialchars_decode($vcontl['content'], ENT_QUOTES);
			$str = trim(strip_tags(str_replace("&nbsp;", ' ', $str)));
			$vcontl['content'] =  mb_substr($str, 0, 88, 'utf-8')."...";
			array_push($newlist, $vcontl);
		}
		//得到顶级分类,
		$this->assign('typeid',$typeid);
		$this->assign('list',$newlist);
		$this->assign('show',$show);
		$this->hotlist();//热点关注
		$this->banner();
		$this->display();
	}
	//更多
	/**
	 * @Title: more 
	 * @Description: todo(读取某分类下的所有文章)   
	 * @author yuansl 
	 * par $LISTNUM 每页显示多少条记录
	 * @date 2014-2-12 上午11:05:26 
	 * @throws
	 */
	public function more(){
		$LISTNUM = 8;
		//查询公司网站
		$model=D("MisSystemCompany");
		$url=$model->where("status=1")->getField('website');
		$this->assign('url',$url);
		
		$keywords = $_REQUEST['keywords'];
		$Map['mis_knowledge_list.status'] = array('eq','1');
		$Map['mis_knowledge_list.type'] = array('eq','Q');
		$Map['mis_knowledge_list.auditState'] = array('eq',3);//xss
		$Map['mis_knowledge_type.status'] = array('eq','1');
		if($keywords == null && !isset($keywords)){
			$typeid = $_REQUEST['typeid'];
			$this->assign('typeid',$typeid);
			$Map['mis_knowledge_type.parentid'] = $typeid;
		}else{
			$this->assign('keywords',$_REQUEST['keywords']);
			//1.输入的是作者 2.输入的是时间格式 3.输入的是文章标题或简介
			//时间
			$keywords=str_replace(' ', '', $keywords);
			$rule='/^(([1-2][0-9]{3}-)((([1-9])|(0[1-9])|(1[0-2]))-)((([1-9])|(0[1-9])|([1-2][0-9])|(3[0-1]))))( ((([0-9])|(([0-1][0-9])|(2[0-3]))):(([0-9])|([0-5][0-9]))(:(([0-9])|([0-5][0-9])))?))?$/';
			$bool=preg_match($rule,$keywords);
			if($bool){
// 				$Map['mis_knowledge_list.createtime'] =array('egt',strtotime($keywords));
// 				$Map['mis_knowledge_list.createtime'] =array('elt',strtotime($keywords)+3600*24);
				$Map['mis_knowledge_list.createtime'] = array(array('egt',strtotime($keywords)),array('elt',strtotime($keywords)+3600*24), 'and');
			}
			//作者
			$userMode = D('User');
			$condit['name'] = array('eq',$_REQUEST['keywords']);//如果该员工是离职状态默认还是能找到他发表的文章
			$re = $userMode->where($condit)->field('id')->find();
			if(count($re)>0){
				$Map['mis_knowledge_list.createid']=array('eq',$re['id']);
			}
			//检索只标题
			if(!$bool && (count($re)==0)){
				$Map['_string'] = "(mis_knowledge_list.title like "."'%".$keywords."%'".")  OR ( mis_knowledge_list.title like "."'%".$keywords."%'".") ";
			}
			//检索文章标题或内容
// 			if(!$bool && (count($re)==0)){
// 				$Map['_string'] = "(mis_knowledge_list.title like "."'%".$keywords."%'".")  OR ( mis_knowledge_list.content like "."'%".$keywords."%'".") ";
// 			}
		}
		//拿出子级下的所有文章记录
		$MisKnowledgeTypeViewMode = D('MisKnowledgeTypeView');
		//分页
		import('ORG.Util.Page');
		$count  = $MisKnowledgeTypeViewMode->where($Map)->count();
		$page   = new Page($count,$LISTNUM);//设置每页显示条数
		foreach($Map as $key=>$val) {
						$page->parameter  .= "$key=".urlencode($val)."&";
					}
		$page->setConfig('theme','%totalRow% %header% %nowPage%/%totalPage% 页 %upPage% %first% %prePage% %linkPage% %nextPage% %end% %downPage%');//自定义分页
		$page->setConfig('header','篇');
//$page->setConfig('prev','上一篇');
//$page->setConfig('next','下一篇');
		$show   = $page->show();
		$AllArcList = $MisKnowledgeTypeViewMode->where($Map)->limit($page->firstRow.','.$page->listRows)->order("createtime desc")->select();
		$newAllArcList = array();
		foreach($AllArcList as $varcl){
			$str = htmlspecialchars_decode($varcl['content'], ENT_QUOTES);
			$str = trim(strip_tags(str_replace("&nbsp;", ' ', $str)));
			$varcl['content'] =  mb_substr($str, 0, 88, 'utf-8')."...";
			array_push($newAllArcList, $varcl);
		}
// 		echo $MisKnowledgeTypeViewMode->getlastSql();
		//栏目 
		$this->banner();
		//热点关注
		$this->hotlist();
		$this->assign('AllArcList',$newAllArcList);
		$this->assign('show',$show);
		$this->display();
	}
	/**
	 * @Title: banner 
	 * @Description: todo(本模块公用方法,获取栏目列表)   
	 * @author yuansl 
	 * @date 2014-2-13 下午12:52:29 
	 * @throws
	 */
/* 	public function banner(){
		$MisKnowledgeTypeViewMode = D('MisKnowledgeTypeView');
		$topMap['mis_knowledge_list.status'] = array('eq',1);
		$topMap['mis_knowledge_list.type'] = array('eq','Q');
		$topMap['mis_knowledge_list.auditState'] = array('eq',3);
		$topMap['mis_knowledge_type.status'] = array('eq',1);
		$mistopList=$MisKnowledgeTypeViewMode->where($topMap)->group("parentid")->getField("parentid,name");//parentid=>name 返回值类型
		$listarray = array();
		foreach ($mistopList as $key=>$val){
			$topMap['parentid'] = $key;
			$listarray[$key]['obj']=array('id'=>$key,'name'=>$val);
			$listarray[$key]['list']= $MisKnowledgeTypeViewMode->where($topMap)->limit(20)->order('createtime desc')->limit(30)->select();//通过
		}
// 		dump($listarray);
		$this->assign('listarray',$listarray);
	} */
	//有用   &  无用
	/**
	 * @Title: applaud 
	 * @Description: todo(ajax请求,用户)   
	 * @author yuansl 
	 * @date 2014-2-13 上午9:47:40 
	 * @throws
	 */
	public function applaud(){
		$arcid = $_POST['arcid'];
		$MisKnowledgeCountModel = D('MisKnowledgeCount');
		$ispuandno=$_POST['parisetype'];//传过来的是赞还是踩    1----赞  2----踩
		$data['arcticleid'] = $_POST['arcid'];
		$data['manid'] = $_SESSION[C('USER_AUTH_KEY')];
		$data['clicktime'] = time();
		$data['ip'] = get_client_ip();
		//判断该用户是是否已经点过
		$rexid = $MisKnowledgeCountModel->where("arcticleid = ".$_POST['arcid']." and status = 1 and manid= ".$_SESSION[C('USER_AUTH_KEY')])->find();
		//如果即没有赞,同时也没有踩,则可以在数据新增记录
		if(count($rexid)==0){
			//有用
			if($ispuandno==1){
				$data['agree'] = 1;
				$result=$MisKnowledgeCountModel->add($data);
				$MisKnowledgeCountModel->commit();
			}else{
				//无用
				$data['agree'] = 0;
				$result = $MisKnowledgeCountModel->add($data);
				$MisKnowledgeCountModel->commit();
			}
			//求得这篇文章的 "赞" 和 "踩" 的总数
			$agree = $MisKnowledgeCountModel->where('arcticleid = '.$arcid.' and agree = 1')->count();
			$noagree = $MisKnowledgeCountModel->where('arcticleid = '.$arcid.' and agree = 0')->count();
			$arr = array();
			if($agree==null){
				$agree = 0;
			}
			if($noagree==null){
				$noagree = 0;
			}
			$arr['result'] = $result;//返回新增的id
			$arr['agree'] = $agree;//赞的记录总数
			$arr['noagree'] = $noagree;//踩的记录总数
			echo  json_encode($arr);
			exit;
		}else{
			$arr = array();
			$arr['result'] = 'faile';
			$arr['agree'] = $agree;//赞的记录总数
			$arr['noagree'] = $noagree;//踩的记录总数
			echo  json_encode($arr);
			exit;
		}
	}
	/**
	 * @Title: expertquestion 
	 * @Description: todo(专家库首页数组展示)   
	 * @author yuansl 
	 * par  $WaiteNum     此参数用于配制专家库首页 "等你来答" 数据条数
	 * par  $expertN      此参数用于配置"专家展示"显示专家个数(建议配置偶数个数)
	 * @date 2014-2-19 下午9:38:15 
	 * @throws
	 */
	public function expertquestion(){
		$this->weburl();
		$this->islogin();
		//$this->leftmessage();
		//公司网站
		$model=D("MisSystemCompany");
		$url=$model->where("status=1")->getField('website');
		$this->assign('url',$url);
		
		$WaiteNum = 2;
		$expertN = 15 ;
		//查找专家问题分类
		$mTypeModel = D('MisExpertQuestionType');
		$MisExpertListModel=D('MisExpertList');
		//类型数组
		$typearr = array();
		//条件数组
		$map = array();
		$map['status'] = 1;
		$map['pid'] = 0;
		$list = $mTypeModel->where($map)->select();
		foreach ($list as $key=>$val){
			$map = array();
			$map['status'] = 1;
			$map['pid'] = $val['id'];
			$sonlist = $mTypeModel->where($map)->select();
			$typearr[$key]['pid'] = $val['id'];
			$typearr[$key]['name'] = $val['name'];
			$typearr[$key]['sontype'] = $sonlist;
		}
		//将类型数组传到页面上
		$this->assign('typearr',$typearr);
		//当前登录人问题列表
		$mQuestionlistModel = D('MisExpertQuestionList');
		$mapcount = array();
		$mapcount['status'] = 1;
		$mapcount['type'] = 'Q';
		$mapcount['closedbyid'] = 0;
		$mapcount['parentid'] = 0;
		$mapcount['createid'] = $_SESSION[C('USER_AUTH_KEY')];
		//我提问的总数
		$count = $mQuestionlistModel->where ($mapcount)->count();
		$this->assign('count',$count);
		//我回答的总数
		$mapanswer = array();
		$mapanswer['status'] = 1;
		$mapanswer['parentid'] = array('NEQ',0);
		$mapanswer['closedbyid'] = array('eq',0);
		$mapanswer['type'] = array('eq','A');
		$mapanswer['createid'] = $_SESSION[C('USER_AUTH_KEY')];
		$answerCount = $mQuestionlistModel->where( $mapanswer )->field("parentid")->group("parentid")->count();
		$this->assign('answerCount',$answerCount);
		//把当前登录人的信息传到页面上
		$this->assign('loginPeople',$_SESSION[C('USER_AUTH_KEY')]);
		//查找两条最新数据放到"等你来答"
		$whx =  "status = 1 and closedbyid = 0 and  parentid = 0 and type = 'Q' and expertid=".$_SESSION[C('USER_AUTH_KEY')];
		$twolist = $mQuestionlistModel->where($whx)->order('id desc,sort asc')->limit($WaiteNum)->select();
		//查询每条问题的回答数
		$newtwolist = array();
		foreach($twolist as $vatw){
			$countsa = $mQuestionlistModel->where("status = 1 and type = 'A' and parentid = ".$vatw['id'])->count('*');
			$vatw['countsa'] = $countsa;
			array_push($newtwolist, $vatw);
		}
		$this->assign('twolist',$newtwolist);
		$this->assign('twolistcount',count($twolist));
		$maptype = array();
		$maptype['type'] = 'A';
		//专家排行榜,取前五位专家的信息
		$experarr = $MisExpertListModel->where("status = 1")->getField('userid',true);
		$ExperUidList = $experarr;//纯id集合
		$cond['status'] = array('eq',1);
		$cond['type'] = array("eq","A");
		$cond['closedbyid'] = array('eq',0);//确认该问题没有被关闭
		$cond['createid'] = array('in',$ExperUidList);
		//该条回答的id=>回答人的id;问题的id是唯一的,createid 是有重复的(有一种情况是一个人回答同一个问题多次)
// 		$List = $mQuestionlistModel->where($cond)->getField("id,createid",true);
		$List = $mQuestionlistModel->where($cond)->getField("id,createid");
// 		dump($List);//测试点
		if($List){//存在专家且回答了问题
			$ExpPx = array_count_values($List);//注,专家userid=>"回答数"
			if(count($ExpPx) > 1){
				arsort($ExpPx);//根据回答数量从高到低排序
			}
			//取出5条数据
			$ix = 0 ;
			$NewExpPx = array();
			foreach($ExpPx as $key=>$value){
				$NewExpPx[$key] = $value;
				$i = $i + 1;
				if($i > 4){
					break;
				}
			}
			//第一名
			$first = array() ;
			foreach($NewExpPx as $key=>$value){
				$first[$key] = $value;
				break;
			}
			$Newfirst = array();
			foreach ($first as $key=>$value){
				$Newfirst['userid'] = $key ;
				//  			$Newfirst['acount'] = $value;
				$Newfirst['anwserCount'] = $value;
				$Newfirst['typeid'] = $this->getFieldBy($key, 'userid', 'typeid', 'mis_expert_list');
				$Newfirst['typename'] = $this->getFieldBy($Newfirst['typeid'], 'id', 'name', 'mis_expert_question_type');
				$Newfirst['picpath'] = $this->getFieldBy($key, 'userid', 'picpath', 'mis_expert_list'); //专家头像的图片路径是没有的
			}
			$this->assign('first',$Newfirst);
			//剩下四个
			$lastF = array();
			$temp = array();//中间变量
			$l = 0 ;
			foreach($NewExpPx as $key=>$value){
				$l = $l + 1 ;
				if($l > 1){
					$temp['userid'] = $key;
					$temp['anwserCount'] = $value;
					array_push($lastF, $temp);
					$temp = array();//清空中间变量
				}
			}
			if(count($lastF) > 0){
				$this->assign('lastF',$lastF);
			}
		}else{//存在专家但没回答问题.默认取五个专家排序没有意义(排行是根据回答问题的数量来的)
			$ExpertListArr = $MisExpertListModel->where("status = 1")->limit(5)->order('deptid , sort')->select();
			//第一名
			$first = $ExpertListArr[0];
			$first['anwserCount'] = 0;
			$first['typeid'] = $this->getFieldBy($first['userid'], 'userid', 'typeid', 'mis_expert_list');
			$first['typename'] = $this->getFieldBy($first['typeid'], 'id', 'name', 'mis_expert_question_type');
			$first['picpath'] = $first['picpath']; 
			$this->assign('first',$first);
			//后面四名
			$lastF = $ExpertListArr;
			unset($lastF[0]);
			$newLastFour = array();
			foreach($lastF as $valts){
				$valts['anwserCount'] = 0 ;
				array_push($newLastFour,$valts);
			}
			$this->assign('lastF',$newLastFour);
		}
		//取两$expertN专家信息
		$MisExpertListMode = D('MisExpertList');
		$expertfirst = $MisExpertListMode->where("status = 1")->order("sort")->limit($expertN)->select();
		$expertfirstcount = count($expertfirst);
		$this->assign('expertfirstcount',$expertfirstcount);
		//给专家组装两个个字段 问题总数totalarc   ,头像存储路劲picpath
		$misHrPersonnelPersonInfoModel=D("MisHrPersonnelPersonInfo");
		$newexpertfirst = array();
		foreach($expertfirst as $vaa){
			//该专家已经解决的问题总数
			$totalarc = $mQuestionlistModel->where("type = 'A' and closedbyid = 0 and status = 1 and createid = ".$vaa['userid'] )->field('id')->count();
			if($totalarc){
				$vaa['totalarc'] = $totalarc;
			}else{
				$vaa['totalarc'] = 0;
			}
			//用户部门
			$vaa['user_depid'] = getFieldBy($vaa['userid'], 'id', 'dept_id', 'user');
			array_push($newexpertfirst, $vaa);
			}
// 			dump($newexpertfirst);
		$this->assign('expertfirst',$newexpertfirst);
		$this->typelist();//左边的分类展开
// 		//取得当前用户的头像
// 		$currentUser =array();
// 		$currentUser['id'] = $_SESSION[C('USER_AUTH_KEY')];
// 		$currentUser['picpath'] = getFieldBy($currentUser['id'], 'userid', 'picpath', "mis_expert_list");
// 		$currentUser['expertid'] = getFieldBy($_SESSION[C('USER_AUTH_KEY')], 'userid', 'id', 'mis_expert_list');
// 		$currentUser['userid'] = $currentUser['id'];
// 		$this->assign('currentUser',$currentUser);
		//获取当前用户头像
		$employeidp = getFieldBy($_SESSION[C('USER_AUTH_KEY')], 'id', 'employeid', 'User');
		$userpicpath = getFieldBy($employeidp, 'id', 'picture', 'mis_hr_personnel_person_info');
		$curruserinfor['userid'] = $_SESSION[C('USER_AUTH_KEY')];
		$curruserinfor['picpath'] = $userpicpath;
		$curruserinfor['name'] = getFieldBy($_SESSION[C('USER_AUTH_KEY')], 'id', 'name', 'User');
		
		//当前用户是否是专家 如果是专家 就传专家的userid,如果不是就传0
		$MisExpertListModel = D("MisExpertList");
		$idexp = $MisExpertListModel->where("status = 1 and userid = ".$_SESSION[C('USER_AUTH_KEY')])->find();
		if($idexp){
			$curruserinfor['isexp'] = 1;
			$curruserinfor['expertid'] = $idexp['id'];
		}else{
			$curruserinfor['isexp'] = 0;
		}
		$this->assign('currentUser',$curruserinfor);
// 		dump($currentUser);
		//判断当前用户是否已登录
		if( $_SESSION[C('USER_AUTH_KEY')]== NULL || !isset($_SESSION[C('USER_AUTH_KEY')])){
			$this->assign('link',false);
		}else{
			$this->assign('link',true);
		}
		//左侧专家分类
		$this->getexoerttypes(1);
		$this->display();
	}
	/**
	 * @Title: getuserpic 
	 * @Description: todo(取得用户头像) 
	 * @param int  $personid  (如果没有穿参数表示获取当前用户的图片信息)
	 * @author yuansl 
	 * @date 2014-1-22 下午5:01:39 
	 * @throws
	 */
	public function getuserpic($personid=''){
		//读取用户头像  dirct by 2014.01.21  
		$misHrPersonnelPersonInfoModel=D("MisHrPersonnelPersonInfo");
		if($personid == null){
			$personid = $_SESSION[C('USER_AUTH_KEY')];
		}
		$userpic = $misHrPersonnelPersonInfoModel->where('id='.$personid)->field("picture")->find();
		$this->assign('userpic',$userpic['picture']);
		$this->assign('userid',$personid);
	}
	//左侧部分公共部分
// 	public function leftmessage(){
// 		//获取当前登录人部门id 角色id
// 		$userModel=M("user");
// 		$userList=$userModel->where("id=".$_SESSION[C('USER_AUTH_KEY')])->find();
// 		//查询公司网站
// 		$model=D("MisSystemCompany");
// 		$url=$model->where("status=1")->getField('website');
// 		$this->assign('url',$url);
// 		//
// 		$MisSystemAnnouncementModel=D('MisSystemAnnouncement');
// 		//查询主类型
// 		$typemodel=D("MisSystemAnnouncementSet");
// 		if( !isset($_SESSION['a']) ){//不是管理员 只能看到在范围内的公告
// 			$map['_string']="( (scopetype=2 and ( (find_in_set('".$userList['dept_id']."',deptid) or find_in_set('".$userList['id']."',personid)) or createid=".$_SESSION[C('USER_AUTH_KEY')]."  ) ) or (scopetype=3))";
// 		}
// 		$map['commit']=1;
// 		$map['status']=1;
// 		$time=time();
// 		$map['endtime']=array(array('eq',0),array('gt',$time),'or');
// 		$map['starttime']=array('lt',$time);
// 		$typeList=$MisSystemAnnouncementModel->where($map)->group('type')->getField("id,type");
// 		$list=array();
// // 		foreach ($typeList as $key=>$val){
// // 			$map['type']=$val;
// // 			$list[$val]=$MisSystemAnnouncementModel->where($map)->order("top,sendtime desc")->limit('0,'.C("ANNOUNCEMENT_TYPE_NUM"))->select();
// // // 			echo $MisSystemAnnouncementModel->getLastSql();
// // 		}
// //dirc	
// 		$templist = array();
// 		foreach ($typeList as $key=>$val){
// 			$map['type']=$val;
// 			//$list[]=$MisSystemAnnouncementModel->where($map)->order("top,sendtime desc")->field('id,type,ptype,title')->limit('0,'.C("ANNOUNCEMENT_TYPE_NUM"))->select();
// 			$templist =$MisSystemAnnouncementModel->where($map)->order("top,sendtime desc")->field('id,type,ptype,title')->limit('0,'.C("ANNOUNCEMENT_TYPE_NUM"))->select();
			
// 		}
		
// 		//类型x1 => array(), x2=> array();
// //dirc  
// 		$this->assign("amlist",$list);
// 		$this->assign('commtypelist',$typeList);
// 	}
	/**
	 * @Title: expertlist 
	 * @Description: todo(专家列表)   
	 * @author yuansl 
	 * par     $expertNum  每页显示专家个数
	 * @date 2014-1-21 下午4:45:02 
	 * @throws
	 */
	public function expertlist(){ 
		$this->weburl();
		//$this->leftmessage();
		//配置显示专家的个数
		$expertNum = 20 ;
		//检索专家姓名
		$ename = $_REQUEST['ename'];
		$this->assign('ename',$ename);
		//求出该专家的id
		$eid = $this->getFieldBy($ename, 'name', 'id', 'User');
		//专家分类
		$experTypeModel = D("MisExpertType");
		//判断改id是否为父级
		$typexinfon = $experTypeModel->where("status = 1 and id = ".$_REQUEST['typeid'])->find();
		if($typexinfon['pid'] == 0){//是父级
			$this->assign('topxtypeid',$_REQUEST['typeid']);
		}else{//是子级
			$this->assign('sonxtypeid',$_REQUEST['typeid']);//本身的id
			$this->assign('topxtypeid',$typexinfon['pid']);//父级的id
		}
		//获取子级
		$this->getexoerttypes();
// 		$SonExpertTypeList = $experTypeModel->where("status = 1 and pid > 0")->select();
// 		$this->assign('SonExpertTypeList',$SonExpertTypeList);
// // 		dump($SonExpertTypeList);
// 		$experTypeList = $experTypeModel->where("status = 1 and pid = 0")->select();
// 		$this->assign('experTypeList',$experTypeList);
		//dump($experTypeList);
		$experModel = D("MisExpertList");
			if($eid){
				$map['userid'] = array('eq',$eid);
			}
			$map['status'] = array('eq',1);
			if($_REQUEST['typeid']){
				$map['typeid'] = $_REQUEST['typeid'];
			}
		//分页
		import('ORG.Util.Page');
		$count  = $experModel->where($map)->count();
		$this->assign('count',$count);
		$page   = new Page($count,$expertNum);//默认显示3条数据,每页
		foreach($map as $key=>$val) {
			$Page->parameter   .=   "$key=".urlencode($val)."&";
		}
		$show   = $page->show();
		$list = $experModel->where($map)->limit($page->firstRow.','.$page->listRows)->order("sort")->select();	
// 		dump($list);
		//组装每个的回答总数
		$MisExpertQuestionListModel = D("MisExpertQuestionList");
		$newList =  array();
		foreach ($list as $pxe){
			$acount = $MisExpertQuestionListModel->where("status = 1 and type = 'A' and closedbyid = 0 and createid = ".$pxe['userid'])->field('parentid')->count('*');
// 			$acount = $MisExpertQuestionListModel->where("status = 1 and type = 'A' and closedbyid = 0 and createid = ".$pxe['userid'])->field('parentid')->group('parentid')->count('*');
			$pxe['acount'] = $acount;
			array_push($newList, $pxe);
		}
		$fixnewlist = array();
		foreach($newList as $vaxl){
			if(strlen($vaxl['tel']) > 12){
				$vaxl['tel'] = substr($vaxl['tel'],0,12)."...";
			}
			array_push($fixnewlist, $vaxl);
		}
// 		$this->assign('allexpertlist',$newList);
		$this->assign('allexpertlist',$fixnewlist);
// 		dump($fixnewlist);
		$this->assign('show',$show);
		//获取当前用户头像
		$employeidp = getFieldBy($_SESSION[C('USER_AUTH_KEY')], 'id', 'employeid', 'User');
		$userpicpath = getFieldBy($employeidp, 'id', 'picture', 'mis_hr_personnel_person_info');
		$curruserinfor['userid'] = $_SESSION[C('USER_AUTH_KEY')];
		$curruserinfor['picpath'] = $userpicpath;
		$curruserinfor['name'] = getFieldBy($_SESSION[C('USER_AUTH_KEY')], 'id', 'name', 'User');
		//当前用户是否是专家 如果是专家 就传专家的userid,如果不是就传0
		$MisExpertListModel = D("MisExpertList");
		$idexp = $MisExpertListModel->where("status = 1 and userid = ".$_SESSION[C('USER_AUTH_KEY')])->find();
		if($idexp){
			$curruserinfor['isexp'] = 1;
			$curruserinfor['expertid'] = $idexp[id];
		}else{
			$curruserinfor['isexp'] = 0;
		}
		$this->assign('curruserinfor',$curruserinfor);
		$this->display();
	}
	public function getexoerttypes($group){
		$experTypeModel = D("MisExpertType");
		$SonExpertTypeList = $experTypeModel->where("status = 1 and pid > 0")->select();
		$experTypeList = $experTypeModel->where("status = 1 and pid = 0")->select();
		if(!$group){
			$this->assign('SonExpertTypeList',$SonExpertTypeList);
			$this->assign('experTypeList',$experTypeList);
		}else{
			$toptypeids = $experTypeModel->where("status = 1 and pid = 0")->getField("id",true) ;
			$typelistarray = array();
			foreach($toptypeids as $val){
				$typelistarray[$val]['sontype'] = $experTypeModel->where("status = 1 and pid=".$val)->field("id,name")->select();
				$typelistarray[$val]['toptype'] = $experTypeModel->where("status = 1 and id=".$val)->field("id,name")->find();
			}
// 			dump($typelistarray);
			$this->assign('typelistarray',$typelistarray);
		}
		
	}
	/**
	 * @Title: myquestion 
	 * @Description: todo(我的提问)   
	 * @author yuansl 
	 * par $anCNUM    参数默认显示回答的问题的条数(默认15)
	 * @date 2014-1-21 下午4:45:52 
	 * @throws
	 */
	public function myquestion(){
		$this->weburl();
		$anCNUM = 15;
		$logbool = $this->islogin();
		if(!$logbool){
			$this->redirect('Public/login','未登录或登录超时!' , 0);
		}
		//$this->leftmessage();
		$keywords = $_REQUEST['keywords'];
		$keywords=str_replace(' ', '', $keywords);
		$this->assign('keywords',$keywords);
		$rule='/^(([1-2][0-9]{3}-)((([1-9])|(0[1-9])|(1[0-2]))-)((([1-9])|(0[1-9])|([1-2][0-9])|(3[0-1]))))( ((([0-9])|(([0-1][0-9])|(2[0-3]))):(([0-9])|([0-5][0-9]))(:(([0-9])|([0-5][0-9])))?))?$/';
		$bool=preg_match($rule,$keywords);
		if($bool){
			$condic['createtime'] = array(array('elt',strtotime($keywords)+3600*24),array('egt',strtotime($keywords)),'and');
		}else{
			$condic['_string'] ="(title like '".'%'.$keywords.'%'."' ) OR (content like '".'%'.$keywords.'%'."')";
		}
		$condic['status'] = array('eq',1);
// 		$condic['views'] = array('eq',1);
		$condic['createid'] = array('eq',$_SESSION[C('USER_AUTH_KEY')]);
		$condic['closedbyid'] = array('eq',0);//确认该问题没有被关闭
		$condic['type'] = array('eq',"Q");
		//$condic['createid'] = array('eq',$_SESSION[C('USER_AUTH_KEY')]);
		//咨询专家的问题
		if($_REQUEST['expert'] == 'confir'){
			$condic['createid'] = array('eq',$_SESSION[C('USER_AUTH_KEY')]);
			$condic['expertid'] = array('gt',0);
		}
		//普通提问
		if($_REQUEST['gener']){
			$condic['createid'] = array('eq',$_SESSION[C('USER_AUTH_KEY')]);
			$condic['expertid'] = array('eq',0);
		}
		//我来解答
		if($_REQUEST['anwsq']){
			$condic['createid'] = array('neq',$_SESSION[C('USER_AUTH_KEY')]);
		}
		$MisExpertQuestionListModel= D('MisExpertQuestionList');
		//分页
		import('ORG.Util.Page');
		$count  = $MisExpertQuestionListModel->where($condic)->count();
		$this->assign('count',$count);
		$page   = new Page($count,$anCNUM);//默认显示2条数据
		foreach($condic as $key=>$val) {
			$Page->parameter   .=   "$key=".urlencode($val)."&";
		}
		$show   = $page->show();
		$list = $MisExpertQuestionListModel->where($condic)->order("id desc")->limit($page->firstRow.','.$page->listRows)->select();
// 		echo $MisExpertQuestionListModel->getlastSql();
		//加入回答数anwsercount
		$turelist = array();
		foreach($list as $vasw){
			$anwsercount = $MisExpertQuestionListModel->where("status = 1 and type= 'A' and parentid= ".$vasw['id'])->count();
			$vasw['anwsercount'] = $anwsercount;
			array_push($turelist, $vasw);
		}
		$this->assign('show',$show);
		$this->assign('questionlist',$turelist);
		//获取用户头像
		$employeidp = getFieldBy($_SESSION[C('USER_AUTH_KEY')], 'id', 'employeid', 'User');
		$userpicpath = getFieldBy($employeidp, 'id', 'picture', 'mis_hr_personnel_person_info');
		$curruserinfor['userid'] = $_SESSION[C('USER_AUTH_KEY')];
		$curruserinfor['picpath'] = $userpicpath;
		$curruserinfor['name'] = getFieldBy($_SESSION[C('USER_AUTH_KEY')], 'id', 'name', 'User');
		//当前用户是否是专家 如果是专家 就传专家的userid,如果不是就传0
		$MisExpertListModel = D("MisExpertList");
		$idexp = $MisExpertListModel->where("status = 1 and userid = ".$_SESSION[C('USER_AUTH_KEY')])->find();
		if($idexp){
			$curruserinfor['isexp'] = 1;
			$curruserinfor['expertid'] = $idexp[id];
		}else{
			$curruserinfor['isexp'] = 0;
		}
		$this->assign('curruserinfor',$curruserinfor);
		$this->display();
	}
	/**
	 * @Title: lookupdeletcommon 
	 * @Description: todo(ajax 公共假删除操作)   
	 * @author yuansl 
	 * par 
	 * @date 2014-4-24 下午4:27:00 
	 * @throws
	 */
	public function lookupdeletcommon(){
		$type = $_POST['type'];
		$id = $_POST['id'];
		$data['status'] = -1 ;
		$model = D($type);
		$rex = $model->where("id = ".$id)->save($data);
// 		echo $model->getLastSql();exit;
		$info = array('ok'=>0);
		if($rex){
			$model->commit();
			$info['ok'] = 1 ;
			echo json_encode($info);
		}else{
			echo json_encode($info);
		}
	}
	//公司网站
	private function weburl(){
		//公司网站
		$model=D("MisSystemCompany");
		$url=$model->where("status=1")->getField('website');
		$this->assign('url',$url);
	}
	/**
	 * @Title: expertdetail 
	 * @Description: todo(获取某个专家的详细信息)   
	 * @author yuansl 
	 * par $NUMP 用于配置 页面左侧显示推荐专家的个数 默认 3
	 * par  $expNUm 用于配置显示 专家详细页 专家实战经验的的记录条数 
	 * @date 2014-2-13 上午9:46:10 
	 * @throws
	 */
	public function expertdetail(){
		$this->weburl();
		//$this->leftmessage();
		$this->islogin();
		//配置数据专家详细左侧显示几位的数据
		$NUMP = 3;
		//配置显示专家实战经验记录条数
		$expNUm = 3 ;
		//专家的个人信息
		$expertid = $_REQUEST['experid'];
		$userida = getFieldBy($_REQUEST['experid'], 'id', 'userid', 'mis_expert_list');
		$MisExpertListMolde = D('MisExpertList');
		//实例专家经验
		$MisExpertExperienceModel = D("MisExpertExperience");
		$expertInfo = $MisExpertListMolde->where("status = 1 and id = ".$expertid)->find();
		$expertInfo['expert_deptid'] = getFieldBy($expertInfo['userid'], 'id', 'dept_id', 'User');
		//r容错处理 乳如果没有改专家
		if(!$expertInfo['id']){
			header("content-type:text/html;charset=utf-8");
			$this->redirect('MisSystemNoticeMethod/expertlist','' , 2,'操作非法,即将返回专家列表!');
		}
		$this->assign('expertInfo',$expertInfo);
// 		dump($expertInfo);
	//实战经验(取专家回复的问题)
		$MisExpertQuestionListModel = D('MisExpertQuestionList');
		$map['status'] = array('eq',1);
		$map['type'] = array('eq','C');
		$map['createid'] = array('eq',$expertid);//考虑这里是不是要拿到该问题的id 该条记录的parenid  就是问题的id
		$anwserList = $MisExpertQuestionListModel->where($map)->field('parentid')->order('count desc')->limit($expNUm)->select();
		$anwserList = array_unique($anwserList);
		$this->assign('anwserList',$anwserList);
	//专家经历
		$MisExpertExperienceList = $MisExpertExperienceModel->where("status = 1 and expertid = ". $_REQUEST['experid'])->select();
// 		echo $MisExpertExperienceModel->getLastSql();
// 		dump($MisExpertExperienceList);
		$this->assign('explist',$MisExpertExperienceList);
	//专家答疑
		$conqel['status'] = array('eq',1);
		$conqel['closedbyid'] = array('eq',0);
		$conqel['type'] = array('eq','A');
		$conqel['createid'] = getFieldBy($_REQUEST['experid'], 'id', 'userid', 'mis_expert_list');
		//$conqel['views'] = array('eq',1);
		$AnwExpList = $MisExpertQuestionListModel->where($conqel)->field('parentid')->group('parentid')->select();
		$newAnwExpList = array();
		foreach($AnwExpList as $anperv){
			array_push($newAnwExpList, $anperv['parentid']);
		}
		//求得专家回答过的问题的总条数
		$countQ = count($AnwExpList);
		//分页
		$conpx['status'] = array('eq',1);
		$conpx['closedbyid'] = array('eq',0);
		$conpx['type'] = array('eq','Q');
		$conpx['id'] = array('in',$newAnwExpList);
		import('ORG.Util.Page');
		$count  = $countQ ;
		$page   = new Page($count,3);//配置每页显示默认显示3问题数据
		foreach($conpx as $key=>$val) {
			$Page->parameter   .=   "$key=".urlencode($val)."&";
		}
		$page->setConfig('header','条');
		//$page->setConfig('prev','上一篇');
		//$page->setConfig('next','下一篇');
		$show   = $page->show();
		$list = $MisExpertQuestionListModel->where($conpx)->limit($page->firstRow.','.$page->listRows)->order('id desc')->select();
		//将专家的回答(可以是多条)并入这个list数组
		$newList = array();
			foreach($list as $cval){
				$expAnwList = $MisExpertQuestionListModel->where("parentid = ".$cval['id']." and type = 'A' and createid = ".$userida)->order('id desc')->select();
				$cval['anwserlist'] =  $expAnwList;
				array_push($newList, $cval);
			}
		$this->assign('listcount',count($newList));
		$this->assign('list',$newList);
		$this->assign('show',$show);
		//读取当前专家人数
		$expertCount = $MisExpertListMolde->where("status = 1")->count();
		$this->assign('expertCount',$expertCount);
		//取得当前用户的头像
		//$this->getuserpic($_REQUEST['experid']);
		//左侧为您推荐专家
		$dexperlist = $MisExpertListMolde->where("status = 1")->limit($NUMP)->order('id asc')->select();
		$this->assign("dexperlist",$dexperlist);
		$this->assign("dexperlistcount",count($dexperlist));
		$this->display();
	}
	/**
	 * @Title: myanwser 
	 * @Description: todo(我的回答)   
	 * @author yuansl 
	 * par 	  $quesNUM  此参数用于配置显示问题的条数
	 * @date 2014-1-21 下午4:47:30 
	 * @throws
	 */
	public function myanwser(){
		$this->weburl();
		$logbool = $this->islogin();
		if(!$logbool){
			$this->redirect('Public/login','' , 0);
		}
		$keywords = $_REQUEST['keywords'];
		$this->assign('keywords',$keywords);
		//查回答
		$condic['status'] = array('eq',1);      
		$condic['type'] = array('eq',"A");
		$condic['createid'] = array('eq',$_SESSION[C('USER_AUTH_KEY')]);
		$MisExpertQuestionListModel= D('MisExpertQuestionList');
		//分页
		import('ORG.Util.Page');
// 		$count  = $MisExpertQuestionListModel->where($condic)->field('parentid')->group("parentid")->count();
		//获取回答的问题的个 数
		$CountList = $MisExpertQuestionListModel->where($condic)->field('parentid')->group("parentid")->select();
		$count = count($CountList);
		$this->assign('count',$count);
		//转化二维数组为一维数组
		$NewCountList = array();
		foreach($CountList as $vsg){
			array_push($NewCountList, $vsg['parentid']);
		}
		//向我咨询的问题
		if($_REQUEST['turn'] == "yes"){
			$secondi['expertid'] = array('eq',$_SESSION[C('USER_AUTH_KEY')]);
		}
		$keywords=str_replace(' ', '', $keywords);
		$rule='/^(([1-2][0-9]{3}-)((([1-9])|(0[1-9])|(1[0-2]))-)((([1-9])|(0[1-9])|([1-2][0-9])|(3[0-1]))))( ((([0-9])|(([0-1][0-9])|(2[0-3]))):(([0-9])|([0-5][0-9]))(:(([0-9])|([0-5][0-9])))?))?$/';
		$bool=preg_match($rule,$keywords);
		if($bool){
			$secondi['createtime'] = array(array('egt',strtotime($keywords)),array('elt',strtotime($keywords)+3600*24), 'and');
		}else{
			$secondi['_string'] ="(title like '".'%'.$keywords.'%'."' ) OR (content like '".'%'.$keywords.'%'."')";
		}
		$secondi['status'] = array('eq',1);
		$secondi['type'] = array('eq','Q');
		$secondi['closedbyid'] = array('eq',0);
		$secondi['id'] =  array('in',$NewCountList);
		$page   = new Page($count,$quesNUM);//默认显示15条数据
		foreach($secondi as $key=>$val) {
			$Page->parameter   .=   "$key=".urlencode($val)."&";
		}
		$show   = $page->show();
		$list = $MisExpertQuestionListModel->where($secondi)->limit($page->firstRow.','.$page->listRows)->select();
// 		echo $MisExpertQuestionListModel->getLastSql();
		$this->assign('show',$show);
		$newlist = array();
		//截取,如果标题过长
		foreach ($list as $ans){
			$str = htmlspecialchars_decode($ans['title'], ENT_QUOTES);
			$str = trim(strip_tags(str_replace("&nbsp;", ' ', $str)));
			$ans['content'] =  mb_substr($str, 0, 30, 'utf-8')."...";
			array_push($newlist, $ans);
		}
		//给数组组装一个字段存每条问题的评价数量
		$newlistA = array();
		foreach($list as $newlist){
			$newlist['acount'] =  $MisExpertQuestionListModel->where("status = 1 and type = 'A' and parentid = ".$newlist['id'])->count();
			array_push($newlistA, $newlist);
		}
		$this->assign('queslist',$newlistA);
		$this->assign('listCount',count($newlistA));

		//获取用户头像
// 		$this->getuserpic();
		$employeidp = getFieldBy($_SESSION[C('USER_AUTH_KEY')], 'id', 'employeid', 'User');
		// 		dump($employeidp);
		$userpicpath = getFieldBy($employeidp, 'id', 'picture', 'mis_hr_personnel_person_info');
		// 		dump($userpicpath);
		$curruserinfor['userid'] = $_SESSION[C('USER_AUTH_KEY')];
		$curruserinfor['picpath'] = $userpicpath;
		$curruserinfor['name'] = getFieldBy($_SESSION[C('USER_AUTH_KEY')], 'id', 'name', 'User');
		//当前用户是否是专家 如果是专家 就传专家的userid,如果不是就传0
		$MisExpertListModel = D("MisExpertList");
		$idexp = $MisExpertListModel->where("status = 1 and userid = ".$_SESSION[C('USER_AUTH_KEY')])->find();
		if($idexp){
			$curruserinfor['isexp'] = 1;
			$curruserinfor['expertid'] = $idexp[id];
		}else{
			$curruserinfor['isexp'] = 0;
		}
		// 		dump($curruserinfor);
		$this->assign('curruserinfor',$curruserinfor);
		$this->display();
	}
	/**
	 * @Title: experquestionlist 
	 * @Description: todo(问题列表)   
	 * @author yuansl 
	 * par  $questCOUNT   配置显示问题的条数 默认15条
	 * @date 2014-2-13 下午12:54:54 
	 * @throws
	 */
	public function experquestionlist(){
		$this->weburl();
		//$this->leftmessage();
		$questCOUNT = 15 ;
		$typeid = $_REQUEST['typeid'];
		$keywords = $_REQUEST['keywords'];
		//判断是否是顶级分类
		$MisExpertQuestionTypeModel = D('MisExpertQuestionType');
		$MisExpertQuestionViewModel = D('MisExpertQuestionView');
		$MisExpertQuestionListModel= D('MisExpertQuestionList');
		//条件
		$map['status'] = array("eq",1);
		$map['closedbyid'] = array("eq",0);//该问题没有被关闭
		$map['type'] = array('eq',"Q");
		//搜索条件
		$keywords=str_replace(' ', '', $keywords);
		$this->assign('keywords',$keywords);
		$rule='/^(([1-2][0-9]{3}-)((([1-9])|(0[1-9])|(1[0-2]))-)((([1-9])|(0[1-9])|([1-2][0-9])|(3[0-1]))))( ((([0-9])|(([0-1][0-9])|(2[0-3]))):(([0-9])|([0-5][0-9]))(:(([0-9])|([0-5][0-9])))?))?$/';
		$bool=preg_match($rule,$keywords);
		if($bool){
			$map['createtime'] = array(array('elt',strtotime($keywords)+3600*24),array('egt',strtotime($keywords)),'and');
		}else{
			$map['_string'] ="(title like '".'%'.$keywords.'%'."' ) OR (content like '".'%'.$keywords.'%'."')";
		}
		//根据分类id进入的(超链接后面参数)
		if($typeid != null){
			$relx = $MisExpertQuestionTypeModel->where("status = 1 and id =".$typeid)->field("pid")->find();
			if($relx['pid'] == 0){//这是顶级分类,这里取顶级分类的子级分类下的所有问题MisExpertQuestionViewModel
				$map['pid'] = $typeid;//====
			}
			if($relx['pid'] > 0){//传过来的值是的子级分类
				$map['categoryid'] = $typeid;
			}
		}
		$questionList = $MisExpertQuestionViewModel->where($map)->select();
		//分页
		$count = $MisExpertQuestionViewModel->where($map)->count();
		$page   = new Page($count,$questCOUNT);//默认显示2条数据
		foreach($map as $key=>$val) {
			$Page->parameter   .=   "$key=".urlencode($val)."&";
		}
		$show   = $page->show();
		$list = $MisExpertQuestionViewModel->where($map)->limit($page->firstRow.','.$page->listRows)->order('id desc')->select();
		//回答个数
		$truelist =array();
		foreach ($list as $ques){
			$anwserCount = $MisExpertQuestionListModel->where("status = 1 and type ='A' and parentid=".$ques['questionid'])->count();
			$ques['anwsercount'] = $anwserCount;
			array_push($truelist, $ques);
		}
		$this->assign('show',$show);
		$this->assign('list',$truelist);
// 		dump($truelist);
		$this->assign('listcount',count($truelist));		
		//左侧分类展开
		$this->typelist();
		$this->display();
	}
	/**
	 * @Title: islogin 
	 * @Description: todo(判断用户是否已经登录)   
	 * @author yuansl 
	 * @date 2014-2-20 上午10:29:48 
	 * @throws
	 */
	private function islogin(){
		if( $_SESSION[C('USER_AUTH_KEY')]== NULL || !isset($_SESSION[C('USER_AUTH_KEY')])){
				$this->assign('link',false);
				return false;
			}else{
				$this->assign('link',true);
				return true ;
			}
	}
	/**
	 * @Title: mesetquestion 
	 * @Description: todo(用户提问前置操作,我要提问页面)   
	 * @author yuansl 
	 * @date 2014-2-20 上午11:58:57 
	 * @throws
	 */
	public function mesetquestion(){
		$this->weburl();
		//判断用户是否已登录
		if( $_SESSION[C('USER_AUTH_KEY')]== NULL || !isset($_SESSION[C('USER_AUTH_KEY')])){
			$this->redirect('Public/login','' , 0);
			exit;
		}
		//过渡变量(在此方法中不起作用,只是借助此方法传值)
		$transition = $_REQUEST['userid'];
		$this->assign('transid',$transition);
		//读取问题分类,传到页面
		$MisExpertQuestionModel = D('MisExpertQuestionType');
		$TopTypeList = $MisExpertQuestionModel->where("status = 1 and pid= 0")->field("id,name")->select();
// 		echo $MisExpertQuestionModel->getLastSql();
		//除去没有子级分类的顶级分类
		$lassary = array();
		foreach ($TopTypeList as $vat){
			$count = $MisExpertQuestionModel->where("status = 1 and pid = ".$vat['id'])->count();
			if($count > 0 ){
				array_push($lassary, $vat);
			}
		}
		$this->assign('topTypeList',$lassary);
		//取有效顶级分类的第一个分类下面子类作为默认
		$xtopid = $lassary[0]['id'];
		$SonTypeList = $MisExpertQuestionModel->where("status = 1 and pid= ".$xtopid)->field("id,name")->select();
		$this->assign('sontypelist',$SonTypeList);
		//读取当前专家人数
		$MisExpertListModel = D("MisExpertList");
		$expertCount = $MisExpertListModel->where("status = 1")->count();
		$this->assign('expertCount',$expertCount);
		$newsExpertlist = $MisExpertListModel->where("status = 1")->field("id,userid")->select();
// 		echo $MisExpertListModel->getLastSql();
		$this->assign('newsExpertlist',$newsExpertlist);
// 		dump($newsExpertlist);
// 		//选择专家分类(二级联动)
// 		$MisExpertTypeModel = D("MisExpertType");
// 		$ExpertTypelist = $MisExpertTypeModel->where("status = 1 ")->field("id,name")->select();
// 		$this->assign('ExpertTypeList',$ExpertTypelist);
// 		$this->assign('ExpertTypeListcount',count($ExpertTypelist));
// 		//默认第一个分类下的人员
// 		$DefaultType = $ExpertTypelist[0]['id'];
// 		$DefaultExpertlist = $MisExpertListModel->where("status = 1 and typeid = ".$DefaultType)->field("id,userid,typeid")->select();
// 		$this->assign('DefaultExpertlist',$DefaultExpertlist);
		$this->display();
	}
	/**
	 * @Title: uploadimg 
	 * @Description: todo(整合UE编辑器上传文件)   
	 * @author yuansl 
	 * @date 2014-3-12 下午3:39:44 
	 * @throws
	 */
// 	public  function uploadimg(){
// 		echo "您已经成功进入图片上传函数中!";
// 	}
	/**
	 * @Title: getexpertlist 
	 * @Description: todo(ajax请求,获取某分类下的专家列表)   
	 * @author yuansl 
	 * @date 2014-2-11 下午4:15:50 
	 * @throws
	 */
	public function getexpertlist(){
		$typeid = $_REQUEST['bepart'];
		$MisExpertListModel = D("MisExpertList");
		$Expertlist = $MisExpertListModel->where("status = 1 and typeid = ".$typeid)->field('id,userid,typeid')->select();
		//组装数据
// 		$UserModel = D("User");
		$NewExpertlist = array();
		foreach ($Expertlist as $vexp){
			$name = $this->getFieldBy($vexp['userid'], 'id', 'name', 'user');
			$vexp['name'] = $name;
			array_push($vexp, $name);
			array_push($NewExpertlist, $vexp);
		}
		echo  json_encode($NewExpertlist);
	}
	
	/**
	 * @Title: handinq 
	 * @Description: todo(处理用户提交的问题数据)   
	 * @author yuansl 
	 * @date 2014-2-13 上午9:43:58 
	 * @throws
	 */
	public function handinq(){
		$logbool = $this->islogin();
		if(!$logbool){
			$this->redirect('Public/login','登录超时,重新登录!' , 0);
			exit;
		}
		if(is_numeric($_REQUEST['transid'])){
			$data['expertid'] = $transid;
		}else{
			$data['expertid'] = $_REQUEST['expertid'];
		}
// 		dump($_REQUEST);exit;
		$data['title'] = $_POST['title'];
		$data['createid'] = $_SESSION[C('USER_AUTH_KEY')];
		$data['content'] = $_POST['content'];
		$data['createtime'] = time();
		$data['categoryid'] =  $_POST['categoryid'];
		$data['type'] = 'Q';
		$data['parentid'] = 0;
		$MisExpertQuestionListModel = D("MisExpertQuestionList");
		$bool = $MisExpertQuestionListModel->add($data);
		//附件保存
		$this->insertkon_swfx($bool);
		if($bool){
			$MisExpertQuestionListModel->commit();
			$this->redirect('MisSystemNoticeMethod/myquestion', array(), 0, '提交成功!');
		}else{
			$MisExpertQuestionListModel->rollback();
			$this->error("提交失败,请稍请重试!");
		}
	}
	public  function insertkon_swfx($insertid){
		$this->swf_upload($insertid,98);
	}
	//上传处理
	/**
	 * @Title: anwserdetail 
	 * @Description: todo(获取提问详细anwserdetail)   
	 * @author yuansl 
	 * @date 2014-2-13 上午9:41:58 
	 * @throws
	 */
	public function anwserdetail(){
		$this->islogin();
		$quesid = $_REQUEST['quesid'];
		$this->assign('quesid',$quesid);
	//取出这个条问题的评论
		$MisExpertQuestionListModel = D("MisExpertQuestionList");
		$ques = $MisExpertQuestionListModel->where("status = 1 and type= 'Q' and closedbyid = 0 and id=".$quesid)->find();
		//如果该问题已被关闭,时候处理方法
			if(count($ques) == 0){
				$this->redirect('MisSystemNoticeMethod/expertquestion', '', 2, '该问题可能被屏蔽,转至主页...');
			}
		//组装一个顶级分类名称topid
		$MisExpertQuestionTypeModel = D('MisExpertQuestionType');
		//取得当前文章的分类的categoryid
		$questypeid = $MisExpertQuestionListModel->where("status =1 and id = ".$quesid)->field("categoryid")->find();
		//根据分类的id在分类表中取得顶级分类的id
		$quesTop = $MisExpertQuestionTypeModel->where("status =1 and id = ".$questypeid['categoryid'])->field("pid")->find();
		$ques['topid'] = $quesTop['pid'];
		$this->assign('ques',$ques);
		//点击数+1
		$count=$ques['count']+1;
		$data['count'] = $count;
		$data['id'] = $quesid;
		$MisExpertQuestionListModel->save($data);
// 		$this->transaction_model->commit();//事务提交
		$MisExpertQuestionListModel->commit();
// 		拿到改条记录下面的回答总数countanwser type = "A"
		$countAnwser = $MisExpertQuestionListModel->where("status = 1 and type= 'A' and parentid=".$quesid)->count();
		$this->assign('countAnwser',$countAnwser);
		//得到所有的回答
		$anwerList = $MisExpertQuestionListModel->where("status = 1 and type= 'A' and parentid=".$quesid)->order("id desc")->select();
		$this->assign('anwerList',$anwerList);
		$lassary = array();
		foreach($anwerList as $valt){
			//查询每条回答的所有评论comments   type = 'C'
			$comments = $MisExpertQuestionListModel->where("status = 1 and type= 'C' and closedbyid = 0 and parentid=".$valt['id'])->order("id desc")->select();
			//查询该条记录的追问(一个问题只允许存在一条追问)
			$addques = $MisExpertQuestionListModel->where("status = 1 and type= 'ASK' closedbyid = 0 and parentid=".$valt['id'])->find();//$quesid
			//组合数组
			$lassary[$valt['id']]['comments'] = $comments;
			$lassary[$valt['id']]['addques'] = $addques;
			$lassary[$valt['id']]['anwser'] = $valt;
		}
		$this->assign('lassary',$lassary);
	//附件操作
		$MisAttachedRecordModel=M('MisAttachedRecord');
		$condition['type'] = 98;
		$condition['tableid'] = $quesid;
		$condition['tablename'] = 'MisSystemNoticeMethod';
		$attachedarr=$MisAttachedRecordModel->where($condition)->select();
		if($attachedarr){
			$this->assign('attarry',$attachedarr);
		}
		$userName = $this->getFieldBy($_SESSION[C('USER_AUTH_KEY')], 'id', 'name', 'user');
		$this->assign('userName',$userName);
		$this->display();
	}
	/**
	 * @Title: addcomment 
	 * @Description: todo(ajax 新增评论)   
	 * @author yuansl 
	 * @date 2014-2-11 上午11:30:12 
	 * @throws
	 */
	public function addcomment(){
		$logbool = $this->islogin();
		if(!$logbool){
			echo -1;
			exit;
		}
		$data['content'] = $_POST['comment'];
		$data['parentid'] = $_POST['parentid'];
		$data['createid'] = $_SESSION[C('USER_AUTH_KEY')];
		$data['createtime'] = time();
		$data['type'] = 'C';
		$MisExpertQuestionListModel = D("MisExpertQuestionList");
		$result = $MisExpertQuestionListModel->add($data);
// 		$this->transaction_model->commit();//事务提交
		$MisExpertQuestionListModel->commit();
		//封装一个username  js 传到前台
		$data['username'] = getFieldBy($_SESSION[C('USER_AUTH_KEY')], 'id', 'name', 'user');
		//转化时间格式
		$data['createtime'] = date('Y-m-d H:i:s',$data['createtime']);
		if(is_int($result) && $result > 1){
			//将插入数据的类容拿到到页面去显示
			echo json_encode($data);
		}else {
			$result = -1;//代表插入数据失败
			echo $result;
		}
	}
	/**
	 * @Title: addanwser 
	 * @Description: todo(处理用户提交回答数据)   
	 * @author yuansl 
	 * @date 2014-2-13 下午4:53:15 
	 * @throws
	 */
	public function addanwser(){
		$logbool = $this->islogin();
		if(!$logbool){
			$this->redirect('Public/login','登录超时,重庆登录!' , 0);
			exit;
		}
// 		dump($_POST);
		$arcid = $_REQUEST['quesid'];//当前问题id
		$createid = $_SESSION[C('USER_AUTH_KEY')];
		$data['createtime'] = time();
		$data['parentid'] = $_POST['quesid'];
		$data['type'] = 'A';
		$data['content']= $_POST['anwser'];
		$data['createid'] = $_SESSION[C('USER_AUTH_KEY')];
		$MisExpertQuestionListModel = D("MisExpertQuestionList");
		$re = $MisExpertQuestionListModel->add($data);
		$MisExpertQuestionListModel->commit();
		if($re){
			$redir_url = "/MisSystemNoticeMethod/anwserdetail/quesid/".$arcid;
			$this->redirect($redir_url,'',0);//跳转到当前页面
		}else{
			$this->error("操作失败!");
		}
	}
	/**
	 * @Title: getsontypelist 
	 * @Description: todo(ajax请求,取得子类)   
	 * @author yuansl 
	 * @date 2014-1-26 上午12:23:48 
	 * @throws
	 */
	public function getsontypelist(){
		$topid = $_REQUEST['bepart'];
		$MisExpertQuestionTypeModel = D("MisExpertQuestionType");
		$sontypelist = $MisExpertQuestionTypeModel->where("status = 1 and pid = ".$topid)->field('id,name')->select();
		echo  json_encode($sontypelist);
	}
	
	/**
	 * @Title: typelist 
	 * @Description: todo(问题分类展开类型)   
	 * @author yuansl 
	 * @date 2014-1-22 上午10:03:41 
	 * @throws
	 */
// 	public function  typelist(){
// 		//创建问题分类对象
// 		$MisExpertQuestionTypeModel=D('MisExpertQuestionType');
// 		//创建视图对象
// 		$MisExpertQuestionViewModel = D('MisExpertQuestionView');
// 		//展开问题分类,限制展开3个  limit 3
// 		$topMap['mis_expert_question_type.status'] = array('eq',1);
// 		$topMap['mis_expert_question_list.status'] = array('eq',1);
// 		$topMap['mis_expert_question_list.type'] = array('eq','Q');//这里的Q,是代表的 "该条记录是取的问题",如果是回复 将取"C"
// 		//得到所有子类,并且子类下面有问题的顶级分类id
// 		$topTypeList = $MisExpertQuestionViewModel->where($topMap)->group("pid")->field('pid')->select();
// 		//根绝这顶级分类的找到下面的子级分类
// 		$lissary = array();
// 		foreach($topTypeList as $val){
// 			//根绝子类分类的id,得到顶级分类name和id
// 			$lissary[$val['pid']]['self'] = array('id'=>$val['pid']);
// 			$lissary[$val['pid']]['sontype'] =  $MisExpertQuestionTypeModel->where('status = 1 and pid = '.$val['pid'])->order('id asc')->limit(3)->select();
// 		}
// 		$this->assign('lissary',$lissary);
// 		$this->assign('lissarycount',count($lissary));
// 	}   
	public function  typelist(){
				//创建问题分类对象
				$MisExpertQuestionTypeModel=D('MisExpertQuestionType');
				//获取父级
				$QuestionTopTypeList = $MisExpertQuestionTypeModel->where("status = 1 and pid = 0")->field("id,name,pid")->select();
				$lissary = array();
				foreach($QuestionTopTypeList as $val){
					//根绝子类分类的id,得到顶级分类name和id
					$listarray[$val['id']]['self'] = array('id'=>$val['id'],'name'=>$val['name']);
					$listarray[$val['id']]['sonlist'] =  $MisExpertQuestionTypeModel->where('status = 1 and pid = '.$val['id'])->field("id,name")->select();
				}
				$this->assign('listarray',$listarray);
				$this->assign('listarraycount',count($listarray));
// 				dump($listarray);
			}
	/*
	* 添加注，Eagle
	* $value : 查找关键值
	* $key		: 目标表中的字段名
	* $fieldName	: 被检索出来的目标表中的字段名
	* $table 		: 被检索的目标表名
	* $field1		: 第二个条件，表字段名，
	* $value1 		： 第二个条件的值
	* */
	function getFieldBy($value,$key,$fieldName,$table,$field1='',$value1=''){
		$model = D($table);
		//修改此处，加入一个1=1 的条件。方便特殊权限不控制到。
		$map[$key] = $value;
		if(is_array($value)){
			$map[$key] =array("in",$value);
		}
		if($field1){
			//$var = "and '.$field11.'='.$value1.'";
			$map[$field1] = $value1;
		}
		$map['_string'] = "1=1";
		// 	"'.$key.'='.$value.' and 1=1 '.$var.'"
		$data = $model->where($map)->getField($fieldName,true);
		if(count($data)<=1){
			$data=$data[0];
		}
		return $data;
	}
	
}
?>
