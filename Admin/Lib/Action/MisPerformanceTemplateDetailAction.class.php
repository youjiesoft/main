<?php

/**
 * @Title: MisPerformanceTypeAction
 * @Package package_name
 * @Description: todo(人力资源管理-模板明细)
 * @author renling
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-8-1 上午11:59:58
 * @version V1.0
 */
class MisPerformanceTemplateDetailAction extends CommonAction {
	public function _before_add(){
		//排除未完
		$MisPerformanceTemplateDetailModel=D('MisPerformanceTemplateDetail');
		$tempid=$_GET['type'];
		$MisPerformanceTemplateDetailList=$MisPerformanceTemplateDetailModel->where("status=1  and tempid=".$tempid)->getField("id,kpitypeid");
		$typeid=$_GET['type'];
		$this->assign("typeid",$typeid);
		$MisPerformanceTypModel=D('MisPerformanceType');
		$map['type']=2; //指标
		$map['status']=1;
		if($MisPerformanceTemplateDetailList){
			$map['id']=array('not in',array_values($MisPerformanceTemplateDetailList));
		}
		$MisPerformanceTypList=$MisPerformanceTypModel->where($map)->getField('id,name');
		$this->assign("MisPerformanceTypList",$MisPerformanceTypList);
	}
	public function  adddetail(){
		$typeid=$_GET['id'];
		$tempid=$_GET['tempid'];
		$this->assign('tempid',$tempid);
		$MisPerformanceTypModel=D('MisPerformanceType');
		$MisPerformanceKpiModel=D('MisPerformanceKpi');
		$MisPerformanceTemplateDetailModel=D('MisPerformanceTemplateDetail');
		//排除未完
		$MisPerformanceTemplateDetailList=$MisPerformanceTemplateDetailModel->where("status=1  and tempid=".$tempid)->getField("id,kpiid");
		$mapkpi['status']=1;
		if($typeid){
			$mapkpi['typeid']=$typeid;
			$this->assign("typeid",$typeid);
		} 
		if($MisPerformanceTemplateDetailList){
			$mapkpi['id']=array('not in',array_values($MisPerformanceTemplateDetailList));
		}
		$MisPerformanceKpiList=$MisPerformanceKpiModel->where($mapkpi)->field("id,name,remark")->select();
		$this->assign('MisPerformanceKpiList',$MisPerformanceKpiList);
		$this->display();
	}
	public function insert(){
		$MisPerformanceTemplateDetailModel=D('MisPerformanceTemplateDetail');
		$kpiid=$_POST['kpiid'];
		foreach($kpiid as $key=>$val){
			$data['tempid']=$_POST['tempid'];
			$data['kpitypeid']=$_POST['kpitypeid'];
			$data['kpitypeqz']=$_POST['kpitypeqz'];
			$data['kpiid']=$val;
			$data['createtime']=time();
			$data['createid']=$_SESSION[C('USER_AUTH_KEY')];
			$data['kpiscore']=$_POST['kpiscore'][$key];
			$list=$MisPerformanceTemplateDetailModel->data($data)->add ();
			if($list==false){
				$this->error ( L('_ERROR_') );
			}
		}
		$this->success('操作成功');
	}
	public function _before_edit(){
		$id=$_GET['id'];
		$MisPerformanceTemplateDetailModel=D('MisPerformanceTemplateDetail');
		$MisPerformanceTemplateDetailtypeList=$MisPerformanceTemplateDetailModel->where("status=1  and id=".$id)->find();
		$MisPerformanceTemplateDetailList=$MisPerformanceTemplateDetailModel->where("status=1  and tempid=".$MisPerformanceTemplateDetailtypeList['tempid']." and kpitypeid=".$MisPerformanceTemplateDetailtypeList['kpitypeid']."  and kpiid <> ".$MisPerformanceTemplateDetailtypeList['kpiid'])->getField("id,kpiid");
		$MisPerformanceKpiModel=D('MisPerformanceKpi');
		$mapkpi['status']=1;
		$mapkpi['typeid']= $MisPerformanceTemplateDetailtypeList['kpitypeid'];
		if($MisPerformanceTemplateDetailList){
			$mapkpi['id']=array("not in",array_values($MisPerformanceTemplateDetailList));
		}
		$MisPerformanceKpiList=$MisPerformanceKpiModel->where($mapkpi)->field("id,name,remark")->select();
		$this->assign('MisPerformanceKpiList',$MisPerformanceKpiList);
	}
	public function update(){
		$MisPerformanceTemplateDetailModel=D('MisPerformanceTemplateDetail');
		$kpiid=$_POST['kpiid'];
		$i=0;
		foreach($kpiid as $key=>$val){
			$data['tempid']=$_POST['tempid'];
			$data['kpitypeid']=$_POST['kpitypeid'];
			$data['kpitypeqz']=$_POST['kpitypeqz'];
			$data['kpiid']=$val;
			$data['createtime']=time();
			$data['createid']=$_SESSION[C('USER_AUTH_KEY')];
			$data['kpiscore']=$_POST['kpiscore'][$key];
			if($i==0){
				$id=$_POST['id'];
				$list=$MisPerformanceTemplateDetailModel->where('id='.$id)->data($data)->save();
			}else{
				$list=$MisPerformanceTemplateDetailModel->data($data)->add ();
			}
			if($list==false){
				$this->error ( L('_ERROR_') );
			}
			$i++;
		}
		$this->success('操作成功');
	}
}