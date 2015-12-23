<?php
/**
 * @Title: MisSystemPanelGghAction
 * @Package package_name
 * @Description: todo(自定义小块面板（个人首页，公司首页）)";
 * @author 管理员
 * @company 重庆特米洛科技有限公司";
 * @copyright 本文件归属于重庆特米洛科技有限公司";
 * @date 2015-05-18 17:43:48
 * @version V1.08
*/
class MisSystemPanelGghAction extends AutoPanelAction{
	public function setting(){
	}
	public function getConfig(){
	}
	/**
	 * 显示当前面板内容
	 * @Title: showPanel
	 * @Description: todo(页面展示)   
	 * @author 管理员 
	 * @date 2015-05-18 17:43:48
	 * @throws
	 */
	public function showPanel(){
		$submodel=M("mis_system_panel_desing_sub");
		$sublist = $submodel->where("masid=1")->select();
		
		/*
		 * 选项卡数据生成
		 * 1。获取当前选项卡记录
		 * 2。根据选项卡记录查找对应的数据
		 */
		
		
		$scdmodel = D("SystemConfigDetail");
		$map["status"]=1;
		foreach($sublist as $key=>$val){
			$model = $val["modelname"];
			$fields = explode(",",$val["showtitle"]);
			$detailList = $scdmodel->getDetail($model,true,"","sortnum");
			$sublist[$key]['link'] = __APP__."/".$model."/index";
			$sublist[$key]['rel'] = $model;
			foreach($detailList as $k=>$v){
				if(!in_array($v['name'],$fields)){
					unset($detailList[$k]);
				}
			}
			$sublist[$key]["detailList"] = $detailList;
			//具体数据
			$listmodel = D($model);
			$list = $listmodel->where($map)->limit(10)->select();
			$sublist[$key]["list"] = $list;
		}
		$this->assign("sublist",$sublist);
		$this->display("MisSystemPanelDesingMas:news");
	}
}
		