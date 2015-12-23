<?php
/**
 * @Title: MisHrPersonnelIndustrialInjuryInfoAction 
 * @Package package_name 
 * @Description: todo(记录人事工伤信息) 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-1-7 上午8:45:12 
 * @version V1.0
 */
class MisHrPersonnelIndustrialInjuryInfoAction extends CommonAction {
	/** @Title: _filter
	 * @Description: (构造检索条件) 
	 * @author  
	 * @date 2013-5-31 下午3:59:44 
	 * @throws 
	*/
	public function _filter(&$map){
		if ($_SESSION["a"] != 1)$map['status']=array("gt",-1);
		//加载配置文件
		$list = require('./Dynamicconf/System/selectlist.inc.php');
		if($_REQUEST['all']){
			$this->assign('search',$_REQUEST['all']);
			$this->assign('searchname','all');
		}
		//伤势分类
		if($_REQUEST['hurtcondition']){
			if($_REQUEST['hurtcondition']=='all'){//选择的为伤势情况节点
				$hurtconditionArr=array();
				foreach ($list['hurtcondition']['hurtcondition'] as $k=>$v){
					$hurtconditionArr[]=$k;//循环出伤势情况 存放的ID
				}
				$map['hurtcondition']=array('in',$hurtconditionArr);
			}else{
				$map['hurtcondition']=$_REQUEST['hurtcondition'];
			}
			$this->assign('search',$_REQUEST['hurtcondition']);
			$this->assign('searchname','hurtcondition');
		}
		//时间分类
		if($_REQUEST['accidenttime']){
			$suffix=substr($_REQUEST['accidenttime'],-1,1);
			$yeartime=substr($_REQUEST['accidenttime'],0,4);
			$startyear=strtotime($yeartime.'-1-1');
			$endyear=strtotime($yeartime.'-12-31');
			if($suffix=='2'){
				$map['accidenttime']=array(array('gt',$startyear),array('lt',$endyear),'and'); 
			}else if($suffix=='1'){
				$map['accidenttime']=array('lt',$startyear);
			}
			$this->assign('search',$_REQUEST['accidenttime']);
			$this->assign('searchname','accidenttime');
		}
		//地理区域
		if($_REQUEST['hurtdistrict']){
			if($_REQUEST['hurtdistrict']=='all'){//选择的为地理区域节点
				$MSSmodel=D("MisSalesSite");
				$hurtdistrictlist=$MSSmodel->where("status=1")->select();
				unset($hurtdistrictlist['0']);
				$hurtdistrictArr=array();
				foreach ($hurtdistrictlist as $k=>$v){
					$hurtdistrictArr[]=$v['id'];//循环地理区域存放的ID
				}
				$map['hurtdistrict']=array('in',$hurtdistrictArr);
			}else{
				$map['hurtdistrict']=$_REQUEST['hurtdistrict'];
			}
			$this->assign('search',$_REQUEST['hurtdistrict']);
			$this->assign('searchname','hurtdistrict');
		}
		//阶段
		if($_REQUEST["disposestage"]){
			if($_REQUEST['disposestage']=='all'){//选择的为阶段节点
				$disposestageArr=array();
				foreach ($list['disposestage']['disposestage'] as $k=>$v){
					$disposestageArr[]=$k;//循环出阶段  存放的ID
				}
				$map['disposestage']=array('in',$disposestageArr);
			}else{
				$map['disposestage']=$_REQUEST['disposestage'];
			}
			$this->assign('search',$_REQUEST['disposestage']);
			$this->assign('searchname','disposestage');
		}
		//默认查询预截止的记录
		if(!$_REQUEST['all'] && !$_REQUEST['hurtcondition'] && !$_REQUEST['enddate'] && !$_REQUEST[disposestage] && !$_REQUEST['hurtdistrict'] && !$_REQUEST['accidenttime'] ){
// 			$map['enddate']=array(array('gt',time()),array('lt',time()+3*30*24*3600),'and');
		}
		//其他类型
		if($_REQUEST['enddate']){
			if($_REQUEST['enddate'] == 'all'){
				$where['disposestage']=7;
				$where['enddate']=array(array('gt',time()),array('lt',time()+3*30*24*3600),'and');
				$where['_logic'] = 'or';
				$map['_complex'] = $where;
			}else{
				//已完结
				if($_REQUEST['enddate'] == 2){
					$map['disposestage']=$_REQUEST['disposestage'];
				}else if($_REQUEST['enddate'] == 1){//预截止
					$map['enddate']=array(array('gt',time()),array('lt',time()+3*30*24*3600),'and');
				}
			}
			$this->assign('search',$_REQUEST['enddate']);
			$this->assign('searchname','enddate');
		}
	}
	public function _before_index(){
		$this->assign("time",time());
		$this->assign("etime",time()+3*30*24*3600);
		$this->getlefttree();
	}
	public function index() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
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
			$this->display('indexview');exit;
		}
		$this->display ();
		return;
	}
	/**
	 * @Title: _before_add
	 * @Description: todo(打开页面前置函数)
	 * @author liminggang
	 * @throws
	*/
	public function _before_add(){
		$this->comm();
		$scnmodel = D('SystemConfigNumber');
		//自动生成单据编号
		$etverno = $scnmodel->GetRulesNO('mis_hr_personnel_industrial_injury_info');
		$this->assign("orderno", $etverno);
		//订单号可写
		$writable= $scnmodel->GetWritable('mis_hr_personnel_industrial_injury_info');
		$this->assign("writable",$writable);
		$this->assign('nowtime',time());
		$this->assign('userid',$_SESSION[C('USER_AUTH_KEY')]);
	}
	/**
	 * @Title: comm 
	 * @Description: todo(公共查询)
	 * @author libo 
	 * @date 2014-3-15 下午2:06:40 
	 * @throws
	 */
	public function comm(){
		//查询该人员的部门
		$personnelIndustrialInjuryInfoModel=D("MisHrPersonnelIndustrialInjuryInfo");
		$deptid=$personnelIndustrialInjuryInfoModel->where("id=".$_REQUEST['id'])->getField("depaid");
		//部门树
		$model=M("mis_system_department");
		$list =$model->where("status=1")->select();
		$deptlist=$this->selectTree($list,0,0,$deptid);
		$this->assign("deptidlist",$deptlist);
		//查询地理区域
		$MSSmodel=D("MisSalesSite");
		$MSSlist=$MSSmodel->where("status=1")->select();
		$this->assign("MSSlist",$MSSlist);
	}
	/**
	 * @Title: _before_edit 
	 * @Description: todo(编辑前置)   
	 * @author libo 
	 * @date 2014-3-15 下午2:07:01 
	 * @throws
	 */
	public function _before_edit(){
		$this->comm();
	}
	/**
	 * @Title: _before_insert 
	 * @Description: todo(插入前置函数)   
	 * @author  
	 * @date 2013-5-31 下午4:08:58 
	 * @throws 
	*/  
	public function _before_insert(){
		//插入之前，验证编码是否重复。如果重复自动更新一个编码
		$this->checkifexistcodeororder('mis_hr_personnel_industrial_injury_info','orderno');
	}
	/**
	 * @Title: _before_update 
	 * @Description: todo(修改保存前置函数)   
	 * @author  
	 * @date 2013-5-31 下午4:08:58 
	 * @throws 
	*/ 
	public function _before_update(){
		//修改之前，验证编码是否重复。如果重复自动更新一个编码
		$this->checkifexistcodeororder("mis_hr_personnel_industrial_injury_info",'orderno',1);
	}
	/**
	 * @Title: getlefttree 
	 * @Description: todo(获取左侧树)   
	 * @author libo 
	 * @date 2014-3-14 下午6:18:33 
	 * @throws
	 */
	public function getlefttree(){
		//加载配置文件
		$list = require('./Dynamicconf/System/selectlist.inc.php');
		//实例化
		$MHPIIImodel=D("MisHrPersonnelIndustrialInjuryInfo");
		$treez = array(); // 树初始化
		//全部
		$treez[]=array(
				'id' => -1,
				'pId' => 0,
				'name' => '全部',
				'title' => '全部',
				'rel' => "MisHrPersonnelIndustrialInjuryInfoBox",
				'target' => 'ajax',
				'icon' => "",
				'url' => "__URL__/index/all/1/jump/1",
				'open' => true
		);
		//其他类型
		$treez[] = array(
				'id' => 115,
				'pId' => -1,
				'name' => '其他类型',
				'title' => '其他类型',
				'rel' => "MisHrPersonnelIndustrialInjuryInfoBox",
				'target' => 'ajax',
				'icon' => "",
				'url' => "__URL__/index/enddate/all/jump/1",
				'open' => true
		);
		$qttreenode[] = array(
				'id' => 1,
				'pId' => 115,
				'name' =>'预截止',
				'title' =>'预截止',
				'rel' => "MisHrPersonnelIndustrialInjuryInfoBox",
				'target' => 'ajax',
				'icon' => "",
				'url' => "__URL__/index/enddate/1/jump/1",
				'open' => true
		);
		$qttreenode[] = array(
				'id' => 2,
				'pId' => 115,
				'name' =>'已完结',
				'title' =>'已完结',
				'rel' => "MisHrPersonnelIndustrialInjuryInfoBox",
				'target' => 'ajax',
				'icon' => "",
				'url' => "__URL__/index/disposestage/7/jump/1/enddate/2",
				'open' => true
		);
		$treez=array_merge($treez,$qttreenode);
		//伤势分类
		$treez[] = array(
				'id' => 111,
				'pId' => -1,
				'name' => '伤势分类',
				'title' => '伤势分类',
				'rel' => "MisHrPersonnelIndustrialInjuryInfoBox",
				'target' => 'ajax',
				'icon' => "",
				'url' => "__URL__/index/hurtcondition/all/jump/1",
				'open' => true
		);
		//获取伤势分类
		$HClist = $list["hurtcondition"]["hurtcondition"];
		if($HClist){
			foreach ($HClist as $key=>$value){
				$hctreenode[] = array(
						'id' => $key,
						'pId' => 111,
						'name' =>$value,
						'title' =>$value,
						'rel' => "MisHrPersonnelIndustrialInjuryInfoBox",
						'target' => 'ajax',
						'icon' => "",
						'url' => "__URL__/index/hurtcondition/".$key."/jump/1",
						'open' => true
				);
			}
		}
		if($HClist){
			$treez=array_merge($treez,$hctreenode);
		}
		//时间分类
		$treez[] = array(
				'id' => 112,
				'pId' => -1,
				'name' => '时间分类',
				'title' => '时间分类',
				'rel' => "MisHrPersonnelIndustrialInjuryInfoBox",
				'target' => 'ajax',
				'icon' => "",
				'url' => "__URL__/index/accidenttime/all/jump/1",
				'open' => true
		);
		//获取时间分类
		$MHPIIItimelist=$MHPIIImodel->where("status=1")->order('accidenttime desc')->getfield('accidenttime');
		//获取年
		$yealtime=substr(transTime($MHPIIItimelist), 0,4);
		for ($i=$yealtime;$i>$yealtime-4;$i--){
			if($i==$yealtime-3){
				$name="年以前";
				$nname="_1";
			}else{
				$name='年';
				$nname="_2";
			}
			$treenode[] = array(
					'id' => 1,
					'pId' => 112,
					'name' =>$i.$name,
					'title' =>$i.$name,
					'rel' => "MisHrPersonnelIndustrialInjuryInfoBox",
					'target' => 'ajax',
					'icon' => "",
					'url' => "__URL__/index/accidenttime/".$i.$nname."/jump/1",
					'open' => true
			);
		}
		if($treenode){
		$treez=array_merge($treez,$treenode);
		}
		//区域类型
		$treez[] = array(
				'id' => 113,
				'pId' => -1,
				'name' => '区域类型',
				'title' => '区域类型',
				'rel' => "MisHrPersonnelIndustrialInjuryInfoBox",
				'target' => 'ajax',
				'icon' => "",
				'url' => "__URL__/index/hurtdistrict/all/jump/1",
				'open' => true
		);
		//获取区域分类
		$MSSmodel=D("MisSalesSite");
		$hurtdistrictlist=$MHPIIImodel->where("status=1")->field('hurtdistrict')->group('hurtdistrict')->select();
		if($hurtdistrictlist['0']['hurtdistrict']==0){//当没有区域选择时
			unset($hurtdistrictlist['0']);
		}
		if($hurtdistrictlist){
			foreach ($hurtdistrictlist as $k=>$v){
				$MSname=$MSSmodel->where("status=1 and id=".$v['hurtdistrict'])->getField('name');
				$hdtreenode[] = array(
						'id' => $v,
						'pId' => 113,
						'name' =>$MSname,
						'title' =>$MSname,
						'rel' => "MisHrPersonnelIndustrialInjuryInfoBox",
						'target' => 'ajax',
						'icon' => "",
						'url' => "__URL__/index/hurtdistrict/".$v['hurtdistrict']."/jump/1",
						'open' => true
				);
			}
		}
		if($hurtdistrictlist){
			$treez=array_merge($treez,$hdtreenode);
		}
		//阶段
		$treez[] = array(
				'id' => 114,
				'pId' =>-1,
				'name' => '阶段',
				'title' => '阶段',
				'rel' => "MisHrPersonnelIndustrialInjuryInfoBox",
				'target' => 'ajax',
				'icon' => "",
				'url' => "__URL__/index/disposestage/all/jump/1",
				'open' => true
		);
		//获取阶段分类
		$DSlist = $list["disposestage"]["disposestage"];
		if($DSlist){
			foreach ($DSlist as $k1=>$v1){
				$dstreenode[] = array(
						'id' => $k1,
						'pId' => 114,
						'name' =>$v1,
						'title' =>$v1,
						'rel' => "MisHrPersonnelIndustrialInjuryInfoBox",
						'target' => 'ajax',
						'icon' => "",
						'url' => "__URL__/index/disposestage/".$k1."/jump/1",
						'open' => true
				);
			}
		}
		if($DSlist){
			$treez=array_merge($treez,$dstreenode);
		}
		$this->assign("tree",json_encode($treez));
	}
}
?>