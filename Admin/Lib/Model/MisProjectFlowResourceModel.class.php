<?php
/**
 * @Title: MisProjectFlowResourceModel 
 * @Package package_name 
 * @Description: 项目=》任务资源关系模型 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-10-22 上午14:04:15 
 * @version V1.0
 */
class MisProjectFlowResourceModel extends MisSystemFlowResourceModel {
	protected $trueTableName = 'mis_project_flow_resource';
	
	/**
	 * @Title: intoProject
	 * @Description: todo(新建项目时将数据插入到新项目表结构中)
	 * @param checkArr,传入当前项目中，模板任务ID与项目任务id的比较结构
	 * @param projectid 项目ID
	 * @author yangxi
	 * @date 2014-10-21 上午11:00:00
	 * @throws
	 */
	public function intoProject($checkArr){
		//项目资源表
		$MPFRModel=D('MisProjectFlowResource');
		$bool = true;
		foreach($checkArr as $key => $val){
			$data = array();
			$data['id'] = $val['projectformid'];
			$data['projectid'] = $val['projectid'];
			$data['alloterid'] = $val['alloterid']?$val['alloterid']:"";
			$data['executorid'] = $val['executorid']?$val['executorid']:"";
			$data['suoshujuese'] = $val['suoshujuese'];
			$data['createtime'] =  time();
			$data['createid'] = $_SESSION[C('USER_AUTH_KEY')];
			$bool = $MPFRModel->add($data);
			if(!$bool){
				$bool = false;
				break;
			}
		}
		return $bool;
	}
	
	/**
	 * @Title: intoFenPaiData
	 * @Description: todo(任务分派数据插入方法) 
	 * @param array $fenpaiArr 分派数据明细
	 * @param unknown $vo 分派单头数据
	 * @return boolean  
	 * @author liminggang
	 * @date 2015年5月12日 下午3:32:42 
	 * @throws
	 */
	public function intoFenPaiData($fenpaiArr,$vo){
		if($fenpaiArr){
			$data = array();
			$data = $vo;
			$data['xiangmubianma'] = $vo['orderno'];
			$data['xiangmumingchen'] = $vo['name'];
			$data['kehumingchen'] = $vo['customerid'];
			$data['yewuleixing'] = $vo['typeid'];
			$data['xingye'] = $vo['hy'];
			$data['zhuti'] = $vo['zt'];
			$data['chanyelian'] = $vo['cyl'];
			$data['zijinyongtu'] = $vo['daikuanyongtu'];
			unset($data['orderno']);
			unset($data['id']);
			$data['projectid'] = $vo['id'];
			//获取系统需要的内容
			$data['createtime'] = time();
			$data['companyid'] = $this->getCompanyID();
			$data['departmentid'] = $this->getDeptID();
			$data['allnode'] = "MisAutoQzu";
			
			$mis_auto_xchwwDao = M("mis_auto_twidh");
			$mis_auto_xchww_sub_datatable10Dao = M("mis_auto_twidh_sub_fenpeixiangqing");
			//自动生成单号
			//$scnmodel = D('SystemConfigNumber');
			
			foreach($fenpaiArr as $key=>$val){
				//获取编号 里面存在递归，导致速度缓慢
				//$ordernoInfo = $scnmodel->getOrderno("mis_auto_twidh","MisAutoQzu");
				//这里的编码暂时用项目角色加项目ID组合
				$data['orderno'] = $key.$vo['id'];
				//所属部门
				$data['suoshubumen'] = getFieldBy($val[0]['xiangmuzu'], "orderno", "suoshubumen", "MisAutoAhm");
				$data['xiangmujiaose'] = $key;
				//分配人
				$data['fenpeiren'] = $val[0]['fenpeiren'];
				$data['createid'] = $val[0]['fenpeiren'];
				//是否自动分派
				$data['shifuzidongfenpei'] = $val[0]['shifuzidongfenpei'];
				$data['operateid'] = 0;
				$data['auditState'] = 0;
				if($val[0]['shifuzidongfenpei']=="是"){
					$data['operateid'] = 1;
					$data['auditState'] = 3;
				}
				if(count($val)>0){
					//进行数据插入
					$masbool = $mis_auto_xchwwDao->add($data);
					if($masbool){
						$flowwork = array();
						foreach($val as $k=>$v){
							$val[$k]['shifufenpei'] = "是";
							$val[$k]['masid'] = $masbool;
							$val[$k]['xiangmuid'] = $vo['id'];//项目ID
							if($data['auditState'] == 3){
								array_push($flowwork, $v['ziyuanhao']);
							}
						}
						$subbool = $mis_auto_xchww_sub_datatable10Dao->addAll($val);
						if($subbool === false){
							return false;
						}
						if(count($flowwork)>0){
							$mis_project_flow_formDao = M("mis_project_flow_form");
							$where = array();
							$where['id'] = array(' in ',$flowwork);
							$workbol = $mis_project_flow_formDao->where($where)->setField("actualbegintime",time());
							if($workbol === false){
								return false;
							}
						}
					}else{
						return false;
					}
				}
			}
		}
		return true;
	}
	
	
	/**
	 * @Title: getMyProjectIdList
	 * @Description: todo(获取当前用户的项目执行ID列表)
	 * @param userid 用户ID
	 * @author Alex
	 * @date 2014-12-4 下午22:52:00
	 * @throws
	 */
	public function getMyProjectIdList($userid){
		//当前传入的用户能查看的项目ID
		$viewProject = "";
		//实例化项目组表
// 		$mis_project_flow_managerDao = M("mis_project_flow_manager");
// 		$where = array();
// 		//根据用户获取能查看的项目
// 		$where['userid'] = $userid;
// 		$where['suoshujiaose'] = 
// 		$manangerprojectidArr = $mis_project_flow_managerDao->where($where)->getField("id,projectid");
// 		$viewProject .=",".implode(",",$manangerprojectidArr);
		//分派人和执行人
		$mis_project_flow_resourceDao = M("mis_project_flow_resource");
		//根据用户获取能查看的项目
		$where = array();
		$where['executorid'] = $userid;
		$executorprojectidArr = $mis_project_flow_resourceDao->where($where)->getField("id,projectid");
		$viewProject .=",".implode(",",$executorprojectidArr);
		//根据用户获取能查看的项目
		$where = array();
		$where['alloterid'] = $userid;
		$alloterprojectidArr = $mis_project_flow_resourceDao->where($where)->getField("id,projectid");
		$viewProject .=",".implode(",",$alloterprojectidArr);
		//零时协作人员
		$mis_auto_twidh_sub_xiezuochengyuanqingkDao = M("mis_auto_twidh_sub_xiezuochengyuanqingk");
		$where = array();
		$where['chengyuanmingchen'] = $userid;
		$xiezuorenyuan = $mis_auto_twidh_sub_xiezuochengyuanqingkDao->join("mis_auto_twidh as mis_auto_twidh on mis_auto_twidh_sub_xiezuochengyuanqingk.masid = mis_auto_twidh.id")
		->where($where)->getField("projectid,chengyuanmingchen");
		$viewProject .=",".implode(",",array_keys($xiezuorenyuan));
		//去重。
		$readuseridArr = array_unique(array_filter(explode(",", $viewProject)));
		return $readuseridArr;
	}
}
?>