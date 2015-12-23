<?php
/**
 * @Title: MisAutoMbwAction
 * @Package package_name
 * @Description: todo(动态表单_扩展类。本类为用户代码注入入口，系统一旦生成将不再重复生成。 * 						但当用户选为组合表单方案后会更新该文件，请做好备份)
 * @author 汤文志
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2015-07-23 15:36:12
 * @version V1.0
*/
class MisAutoMbwExtendAction extends CommonAction {
	public function _extend_filter(&$map){
	}
	/**
	 * @Title: _extend_before_index
	 * @Description: todo(扩展前置index函数)
	 * @author 汤文志
	 * @date 2015-07-23 15:36:12
	 * @throws 
	*/
	function _extend_before_index() {
	}
	/**
	 * @Title: _extend_before_edit
	 * @Description: todo(扩展的前置编辑函数)
	 * @author 汤文志
	 * @date 2015-07-23 15:36:12
	 * @throws 
	*/
	function _extend_before_edit(){
	}
	/**
	 * @Title: _extend_before_insert
	 * @Description: todo(扩展的前置添加函数)
	 * @author 汤文志
	 * @date 2015-07-23 15:36:12
	 * @throws 
	*/
	function _extend_before_insert(){
	}
	/**
	 * @Title: _extend_before_update
	 * @Description: todo(扩展前置修改函数)  
	 * @author 汤文志
	 * @date 2015-07-23 15:36:12
	 * @throws
	*/
	function _extend_before_update(){
	}
	/**
	 * @Title: _extend_after_edit
	 * @Description: todo(扩展后置编辑函数)
	 * @author 汤文志
	 * @date 2015-07-23 15:36:12
	 * @throws 
	*/
	function _extend_after_edit($vo){
	}
	/**
	 * @Title: _extend_after_list
	 * @Description: todo(扩展前置List)
	 * @author 汤文志
	 * @date 2015-07-23 15:36:12
	 * @throws 
	*/
	function _extend_after_list(){
	}
	
	/**
	 * @Title: setProcessRelation 
	 * @Description: todo(流程转授公用方法) 
	 * @param 当前单据ID $id
	 * @param 当前模型名称 $name  
	 * @author liminggang
	 * @date 2015-7-24 下午4:03:37 
	 * @throws
	 */
	private function setProcessRelation($id,$name){
		//终审后，进行已有流程变更
		$model = D($name);
		$vo = $model->find($id);
		//判断转授人是否选择了，如果未选，则获取制单人
		$userid = $vo['zhuanshouren']?$vo['zhuanshouren']:$vo['createid'];
		$zhuanshouren = getFieldBy($userid,"id","name","user");
		//转授给某人
		$touserid = $vo['zhuanshougei'];
		$zhuanshougei = getFieldBy($touserid,"id","name","user");
		if($touserid){
			//流程审批表
			$mis_work_monitoringDao = M("mis_work_monitoring");
			if($vo['zhuanshoufanwei'] == "全部"){
				$where = array();
				$where['dostatus'] = 0;
				$where['_string'] = 'FIND_IN_SET(  ' . $userid . ',curAuditUser )';
				$worklist = $mis_work_monitoringDao->where($where)->field("id,curAuditUser")->select();
			}else{
				//存在定向制定流程转授模块
				$mis_auto_guikc_sub_datatable6Dao = M("mis_auto_guikc_sub_datatable6");
				$where = array();
				$where['masid'] = $id;
				$sublist = $mis_auto_guikc_sub_datatable6Dao->where($where)->getField("id,zhuanshoumoxing");
				if($sublist){
					$where = array();
					$where['dostatus'] = 0;
					$where['tablename'] = array(' in ',$sublist);
					$where['_string'] = 'FIND_IN_SET(  ' . $userid . ',curAuditUser )';
					$worklist = $mis_work_monitoringDao->where($where)->field("id,curAuditUser")->select();
				}
			}
			if($worklist){
				//存在，进行修改转授人修改
				foreach($worklist as $key=>$val){
					$curAuditUser = explode(",", $val['curAuditUser']);
					foreach($curAuditUser as $k=>$v){
						if($v == $userid){
							$curAuditUser[$k] = $touserid;
						}
					}
					$curAuditUser = implode(",", $curAuditUser);
					//构造修改数据
					$data = array();
					$data['id'] = $val['id'];
					$data['curAuditUser'] = $curAuditUser;
					$data['miaoshu'] = $zhuanshouren."转授给".$zhuanshougei;
					$data['zhuanshou'] = 1;
					$result = $mis_work_monitoringDao->save($data);
					if($result == false){
						$this->error("现有流程转授修改失败");
					}
				}
			}
		}
	}
	
	/**
	 * @Title: _extend_after_insert
	 * @Description: todo(扩展后置insert函数)  
	 * @author 汤文志
	 * @date 2015-07-23 15:36:12
	 * @throws
	*/
	function _extend_after_insert($id){
		//验证是否确认提交。确认提交后。进行进行中的流程修改
		if($_POST['operateid'] == 1){
			$this->setProcessRelation($id, $this->getActionName());
		}
	}
	/**
	 * @Title: _extend_before_add
	 * @Description: todo(扩展前置add函数)  
	 * @author 汤文志
	 * @date 2015-07-23 15:36:12
	 * @throws
	*/
	function _extend_before_add(&$vo){
		$this->getFormIndexLoad($vo);
	}
	/**
	 * @Title: _extend_after_update
	 * @Description: todo(扩展后置update函数)  
	 * @author 汤文志
	 * @date 2015-07-23 15:36:12
	 * @throws
	*/
	function _extend_after_update(){
		//验证是否确认提交。确认提交后。进行进行中的流程修改
		if($_POST['operateid'] == 1){
			$this->setProcessRelation($_POST['id'], $this->getActionName());
		}
	}
	/**
	 * @Title: guoqizhuanshou
	 * @Description: todo(流程转授公用方法)

	 * @author liminggang
	 * @date 2015-7-24 下午4:03:37
	 * @throws
	 */
	//当时间过期后转回当事人
	public function guoqizhuanshou(){
		//记录成功数
		$chenggongshu = 0;
		//获取当前时间戳
		$newtime = time();
		//实例化对象
		$modelMbw = M('mis_auto_guikc');
		$modelMonitoring = M('MisWorkMonitoring');
		$map = array();
		$map['shixiaoriqi'] = array('lt',$newtime);
		//创建流程转售查询
		$listMbw = $modelMbw->where($map)->field('shixiaoriqi,zhuanshougei,zhuanshouren')->select();
		//遍历每行数据
		foreach ( $listMbw as $key => $val ) {
			// 如果当前时间大于失效时间就把审核人变回原来的审核人
			if($newtime>$val['shixiaoriqi']){
				$map = array();
				$map['dostatus'] = '0';
				$map['zhuanshou'] = '1';
				$map['status'] = '1';
				$map['_string'] = 'FIND_IN_SET(  ' .$val['zhuanshougei']. ',curAuditUser )';
				//查找到的人
				$listMonitoring = $modelMonitoring->field('id,curAuditUser,zhuanshou')->where($map)->select();
				//遍历misworkmonitoring中的每行数据 进行更新
				foreach($listMonitoring as $mtkey => $mtval){
					$mtmap = array();
					//以ID来更新
					$mtmap['id'] = $mtval['id'];
					//替换掉审核人
					$mtval['curAuditUser'] = str_replace($val['zhuanshougei'],$val['zhuanshouren'],$mtval['curAuditUser']);
					//另一种更新方法 同时改变转授状态 变回0未转授状态
					$data = array('curAuditUser'=>$mtval['curAuditUser'],'zhuanshou'=>'0','miaoshu'=>'转授时间到期，未处理单据自动收回到原审核人');
					$bool = $modelMonitoring-> where($mtmap)->setField($data);
					if($bool == false){
						echo $modelMonitoring->getlastSql()."执行转授审核人撤回失败sql，执行时间是".transTime(time(),'Y-m-d H:i:s')."\n";
					}
					$chenggongshu++;
				}
			}
		}
		$this->transaction_model->commit();//事务提交
		echo "执行成功".transTime(time(),'Y-m-d H:i:s')."\n";
	}
}
?>