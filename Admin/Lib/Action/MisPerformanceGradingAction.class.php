<?php
/**
 * @Title: MisPerformanceGradingAction 
 * @Package package_name
 * @Description: todo(评分人分类) 
 * @author laicaixia
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-8-26 下午3:36:47 
 * @version V1.0
 */

class MisPerformanceGradingAction extends CommonAction {
	
	/**
	 * @Description: _before_add(进入新增)   
	 * @author laicaixia 
	 * @date 2013-8-1 下午6:13:53 
	*/  
	public function _before_add(){
		//自动生成绩效指标编码
		$scnmodel = D('SystemConfigNumber');
		$code = $scnmodel->GetRulesNO('mis_performance_grading');
		$this->assign("code",$code);
	}
	
	/**
	 * @Description: _before_edit(进入修改)   
	 * @author laicaixia 
	 * @date 2013-8-1 下午6:18:33 
	*/  
	public function _before_edit(){
		//调用公共函数
		$this->common();
	}
	/**
	 * @Description: common(共用信息)   
	 * @author laicaixia 
	 * @date 2013-8-1 下午5:36:03 
	 * @throws 
	*/  
	private function common(){
		//取出绩效评分人类型
		$mptModel = M("mis_performance_type");//绩效考核类型表
		$mptList=$mptModel->where('status = 1 and type = 4')->getField('id,name');
		$this->assign('mptList',$mptList);
	}
}
?>