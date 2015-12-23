<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(绩效评估管理) 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-3-20 下午4:37:32 
 * @version V1.0
 */
class MisHrPersonnelAppraisalInfoAction extends CommonAction{
	/** @Title: _filter
	 * @Description: (构造检索条件) 
	 * @author  
	 * @date 2013-5-31 下午3:59:44 
	 * @throws 
	*/
	public function _filter(&$map) {
		if ($_SESSION["a"] != 1)$map['status'] = 1;
		//判断是不是管理员或者是否为showeval的权限，如果不是则只能看见本人和评审人信息
		if($_SESSION["a"] != 1 && $_SESSION["mishrpersonnelappraisalinfo_showdetail"]!=1){
			$where['createid']=$_SESSION[C('USER_AUTH_KEY')];
			$where['linkone']=array("exp","=".$_SESSION['user_employeid']." and linkonecontent='0'");
			$where['linktwo']=array("exp","=".$_SESSION['user_employeid']." and linktwocontent='0'");
			$where['_logic']='or';
			$map['_complex']=$where;
		}
	}
	/**
	 * @Title: _before_add
	 * @Description: todo(打开页面前置函数)
	 * @author 
	 * @throws
	 */
	public function _before_add(){
		//根据当前登录用户。查出他员工编号
		$employeid=$_SESSION["user_employeid"];
		$model=D("MisHrBasicEmployee");
		$list=$model->where("id=".$employeid)->find();
		$this->assign("list",$list);
		$this->common();
		//订单号可写
		$scnmodel = D('SystemConfigNumber');
		$writable= $scnmodel->GetWritable('mis_hr_evaluation');
		$this->assign("writable",$writable);
		$orderno = $scnmodel->GetRulesNO('mis_hr_evaluation');
		$this->assign("orderno", $orderno);
		$this->assign("time",time());
	}
	/**
	 * @Title: _before_insert 
	 * @Description: todo(插入前置函数)   
	 * @author  
	 * @date 2013-5-31 下午4:08:58 
	 * @throws 
	*/
	public function _before_insert(){
		//序列化项目
		if($_POST['arr_projectname']){
			$peoplearr=array();
			foreach($_POST['arr_projectname'] as $keys=>$val){
				$peoplearr[$keys]['projectname']=$val;
				$peoplearr[$keys]['begintime']=$_POST["arr_begintime"][$keys];
				$peoplearr[$keys]['endtime']=$_POST["arr_endtime"][$keys];
				$peoplearr[$keys]['projectpeople']=$_POST["arr_projectpeople"][$keys];
			}
			$_POST["projectval"]=serialize($peoplearr);
		}
		//调用公共插入方法。1  表示新增调用的这个方法。
		$this->_before_insertupdate("1");
		//编码重复处理
		$this->checkifexistcodeororder('mis_hr_evaluation','orderno');
	}
	/**
	 * @Title: _before_insertupdate 
	 * @Description: todo(新增和修改时，处理验证和插入修改方法)
	 * @param int $bjstatus  状态值，1表示新增，新增后本人就不能对数据进行第二次操作，用户要求的。
	 * @author  
	 * @date 2013-5-31 下午4:08:58 
	 * @throws 
	*/
	private function _before_insertupdate($bjstatus=0){
		//得到当前表的ID;
		$pkid=$_POST['pkid'];
		$name=$this->getActionName();
		$model=D($name);
		$list=$model->where('id='.$pkid)->find();
		//类型序列化
		if($_POST['typename']){
			$_POST["evaltypecontent"]=serialize($_POST['typename']);
		}
		//指标序列化
		$arr=array();
		if($_POST['targetname']){
			foreach($_POST['targetname'] as $key=>$v){
				$arr[$key]['targetname']=$v;
				$arr[$key]['targetzbcontent']=$_POST["targetzbcontent"][$key];
				$arr[$key]['targetzbweight']=$_POST["targetzbweight"][$key];
				$arr[$key]['targetzbtypeid']=$_POST["targetzbtypeid"][$key];
				$arr[$key]['checked']=$_POST[$key];
			}
			$_POST["evaltargetcontent"]=serialize($arr);
		}
		//评估内容序列化
		$arrays=array();
		if($_POST['subinfoid']){
			foreach ($_POST['subinfoname'] as $k=>$v){
				$arrays[$k]['subinfoid']=$_POST['subinfoid'][$k];
				$arrays[$k]['subinfoname']=$v;
				$arrays[$k]['subinfoweight']=$_POST['subinfoweight'][$k];
				$arrays[$k]['subinfoevetypeid']=$_POST['subinfoevetypeid'][$k];
			}
			$_POST["evalcontent"]=serialize($arrays);
		}
		$model=D("MisHrEvaluationRelevance");
		if($_SESSION['user_employeid']==$_POST['personid']){
			if($bjstatus){
				//自评人内容
				if (false === $model->create ()) {
					$this->error ( $model->getError () );
				}
				//保存当前数据对象
				$contid=$model->add ();
				if (!$contid) {
					$this->error ( L('_ERROR_') );
				}
				//将插入的ID返回给主表的自我评价
				$_POST['oneselfcontent']=$contid;
			}else{
				$this->error ( "对不起，您已经自评了！" );
			}
		}
		if($_SESSION['user_employeid']==$_POST['linkone']){
			if($list['linkonecontent']==0){
				//A级评估人内容
				if (false === $model->create ()) {
					$this->error ( $model->getError () );
				}
				//保存当前数据对象
				$contone=$model->add ();
				if (!$contone) {
					$this->error ( L('_ERROR_') );
				}
				//将插入的ID返回给主表的自我评价
				$_POST['linkonecontent']=$contone;
			}else{
				$this->error ( "对不起，您已经评估过了！" );
			}
		}
		if($_SESSION['user_employeid']==$_POST['linktwo']){
			if($list['linktwocontent']==0){
				//B级评估人内容
				if (false === $model->create ()) {
					$this->error ( $model->getError () );
				}
				//保存当前数据对象
				$conttwo=$model->add ();
				if (!$conttwo) {
					$this->error ( L('_ERROR_') );
				}
				//将插入的ID返回给主表的自我评价
				$_POST['linktwocontent']=$conttwo;
			}else{
				$this->error ( "对不起，您已经评估过了！" );
			}
		}
	}
	/**
	 * @Title: showdetail 
	 * @Description: todo(查看所有评估内容)
	 * @author  
	 * @date 2013-5-31 下午4:08:58 
	 * @throws 
	*/
	public function showdetail(){
		//查询主要负责的项目
		$id = $_REQUEST['id'];
		$name=$this->getActionName();
		$model=D($name);
		$list=$model->where('id='.$id)->find();
		//反序列化负责的项目
		$projectval=unserialize($list['projectval']);
		$this->assign('projectval',$projectval);
		$this->assign('vo',$list);
		$relmodel=D("MisHrEvaluationRelevance");
		//自评估内容
		$oneselfcontent=$relmodel->where("id=".$list['oneselfcontent'])->find();
		//A级评估内容
		$linkonecontent=$relmodel->where("id=".$list['linkonecontent'])->find();
		//B及评估内容
		$linktwocontent=$relmodel->where("id=".$list['linktwocontent'])->find();
		//反序列化自评估内容
		$oneselfevaltypecontent=unserialize($oneselfcontent['evaltypecontent']);
		$oneselevaltargetcontent=unserialize($oneselfcontent['evaltargetcontent']);
		$oneselevalcontent=unserialize($oneselfcontent['evalcontent']);
		$onselflist=$this->combinationevalua($oneselfevaltypecontent, $oneselevaltargetcontent, $oneselevalcontent);
// 		print_r($onselflist);
		$this->assign("onselflist",$onselflist);
		//反序列化A级评估内容
		$linkonecontents=unserialize($linkonecontent['evaltypecontent']);
		$linkoneevaltargetcontents=unserialize($linkonecontent['evaltargetcontent']);
		$linkoneevalcontents=unserialize($linkonecontent['evalcontent']);
		$linkonelist=$this->combinationevalua($linkonecontents, $linkoneevaltargetcontents, $linkoneevalcontents);
// 		print_r($linkonelist);
		$this->assign("linkonelist",$linkonelist);
		$this->assign("oneprincipalcontent",$linkonecontent['principalcontent']);
		//反序列化B级评估内容
		$linktwocontents=unserialize($linktwocontent['evaltypecontent']);
		$linktwoevaltargetcontents=unserialize($linktwocontent['evaltargetcontent']);
		$linktwoevalcontents=unserialize($linktwocontent['evalcontent']);
		$linktwolist=$this->combinationevalua($linktwocontents, $linktwoevaltargetcontents, $linktwoevalcontents);
		$this->assign("linktwolist",$linktwolist);
		$this->assign("twoprincipalcontent",$linktwocontent['principalcontent']);
		$this->display();
	}
	/**
	 * @Title: toperson 
	 * @Description: todo(指派评估人)
	 * @author  
	 * @date 2013-5-31 下午4:08:58 
	 * @throws 
	*/
	public function toperson(){
		$name=$this->getActionName();
		$model=D($name);
		$save=$_POST['save'];
		if($save){
			$id=$_POST['pkid'];
			$map['linkone']=$_POST['linkone'];
			$map['linktwo']=$_POST['linktwo'];
			$dzlist=$model->where('id='.$id)->save($map);
			if($dzlist){
				$this->success("指定成功");
			}else{
				$this->error("指定失败");
			}
		}
		//查询主要负责的项目
		$id = $_REQUEST['id'];
		$name=$this->getActionName();
		$model=D($name);
		$list=$model->where('id='.$id)->find();
		//反序列化负责的项目
		$projectval=unserialize($list['projectval']);
		$this->assign('projectval',$projectval);
		//根据
		$this->common();
		$this->assign( 'vo', $list );
		$this->display ();
	}
	/**
	 * @Title: combinationevalua 
	 * @Description: todo(组合评估内容数组，方便在html页面的时候。循环出评估内容)
	 * @param array $list1   评估类型
	 * @param array $list2  评估指标
	 * @param array $list3 评估具体内容
	 * @return Ambigous <multitype:, number, unknown>
	 * @author  
	 * @date 2013-5-31 下午4:08:58 
	 * @throws 
	*/
	private function combinationevalua($list1,$list2,$list3){
		$arraylist=array();
		foreach($list1 as $k=>$v ){
			$arraylist[$k]['id']=$k;
			$arraylist[$k]['name']=$v;
			$i=0;
			foreach($list2 as $k1=>$v1){
				if($v1['targetzbtypeid']==$k){
					if($i==0){
						$arraylist[$k]['zbid']=$k1;
						$arraylist[$k]['zbname']=$v1['targetname'];
						$arraylist[$k]['zbcontent']=$v1['targetzbcontent'];
						$arraylist[$k]['zbweight']=$v1['targetzbweight'];
						$arraylist[$k]['zbtypeid']=$v1['targetzbtypeid'];
						$arraylist[$k]['selected']=$v1['checked'];
						foreach ($list3 as $k2=>$v2){
							if($v2['subinfoevetypeid']==$k1){
								$arraylist[$k]['sublist'][]=$v2;
							}
						}
					}else{
						foreach ($list3 as $k2=>$v2){
							if($v2['subinfoevetypeid']==$k1){
								$v1["sublist"][]=$v2;
							}
						}
						$arraylist[$k]['targets'][]=$v1;
					}
					$i++;
					$arraylist[$k]['count']=$i;
				}
			}
		}
		return $arraylist;
	}
	/**
	 * @Title: _before_edit
	 * @Description: todo(打开修改页面前置函数)   
	 * @author  
	 * @date 2013-5-31 下午4:11:26 
	 * @throws 
	*/  
	public function _before_edit(){
		//查询主要负责的项目
		$id = $_REQUEST['id'];
		$name=$this->getActionName();
		$model=D($name);
		$list=$model->where('id='.$id)->find();
		//反序列化负责的项目
		$projectval=unserialize($list['projectval']);
		$this->assign('projectval',$projectval);
		//根据
		$this->common();
	}
	/**
	 * @Title: _before_edit
	 * @Description: todo(用ztree形式查询出所有部门员工信息,注意：这里只实用与在职人员。如果不是请自己写方法在控制器里。)   
	 * @author  liminggang
	 * @date 2013-5-31 下午4:11:26 
	 * @throws 
	*/  
	public function lookupmanage(){
		$model= M('mis_system_department');
		$deptlist = $model->where("status=1")->order("id asc")->select();
		$param['rel']="positiveBox";
		$param['url']="__URL__/lookupmanage/jump/1/deptid/#id#/parentid/#parentid#";
		$typeTree = $this->getTree($deptlist,$param);
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
		$map['mis_hr_personnel_person_infov.working'] = 1; //在职
		$map['mis_hr_personnel_person_info.status']=1; //正常
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
	 * @Title: _before_update
	 * @Description: todo(修改保存前置函数)   
	 * @author  
	 * @date 2013-5-31 下午4:12:50 
	 * @throws 
	*/ 
	public function _before_update(){
		//调用公共插入方法。
		$this->_before_insertupdate();
		//转换一下ID名称
		$_POST['id']=$_POST['pkid'];
		//编码重复处理
		$this->checkifexistcodeororder('mis_hr_evaluation','orderno',1);
	}
	/**
	 * @Title: common 
	 * @Description: todo(公共调用函数)   
	 * @author 杨东 
	 * @date 2013-5-31 下午4:07:47 
	 * @throws 
	*/  
	public function common($list=array()){
		$map['status']=1;
		//类型
		$model1=D("MisHrEvaluationType");
		$evltplist=$model1->where($map)->select();
		//具体指标
		$model2=D("MisHrEvaluationTarget");
		$evltlist=$model2->where($map)->select();
		//评估内容
		$model3=D("MisHrEvaluationContent");
		foreach ($evltplist as $k=>$v ){
			$evltplist[$k]['count']=1;
			$a=true;
			foreach ($evltlist as $k1=>$v1){
				if($v1['evetypeid']==$v['id']){
					$map["targetid"]=$v1['id'];
					$sublist=$model3->where($map)->select();
					if($a){
						$evltplist[$k]['zbid']=$v1["id"];
						$evltplist[$k]['zcode']=$v1["code"];
						$evltplist[$k]['zbname']=$v1["name"];
						$evltplist[$k]['zbcontent']=$v1["content"];
						$evltplist[$k]['zbweight']=$v1["weight"];
						$evltplist[$k]['zbtypeid']=$v['id'];
						$evltplist[$k]['sublist']=$sublist;
						if(isset($list[$v1['id']])){
							$evltplist[$k]['selected']=$list[$v1['id']];
						}
						$a=false;
					}else{
						$evltplist[$k]['count']+=1;
						$v1['sublist']=$sublist;
						if(isset($list[$v1['id']])){
							$v1['selected']=$list[$v1['id']];
						}
						$evltplist[$k]['targets'][]=$v1;
					}
				}
			}
		}
		$this->assign("evltplist", $evltplist);
		$this->assign("evclist", $evclist);
	}
}
?>