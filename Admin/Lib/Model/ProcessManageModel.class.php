<?php
class ProcessManageModel extends CommonModel {
    protected $trueTableName = 'process_info';
    public $_auto =	array(
            array('createid','getMemberId',self::MODEL_INSERT,'callback'),
            array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
            array('createtime','time',self::MODEL_INSERT,'function'),
            array('updatetime','time',self::MODEL_UPDATE,'function'),
    	    array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),

    		
    );
    /**
     * @Title: setDataJson
     * @Description: todo(格式化流程图标数据) 
     * @param 流程数据 $data
     * @param 是否可操作 $isShow
     * @param 图形连接标记 $htmlid
     * @param 流程走向节点 $isComplete
     * @return string  
     * @author 黎明刚
     * @date 2015年1月22日 下午3:27:33 
     * @throws
     */
    public function setDataJson($data,$isShow,$isComplete){
    	$jsondata = array();
    	$jsondata["data"]["total"]=count($data);// 总数
    	$jsondata["data"]["type"]=1;//类型
    	$jsondata["data"]["show"]=$isShow?$isShow:0;//是否有双击事件
    	$n = 0;// 流程序号 必须
    	foreach ($data as $k => $v) {
    		$val = array();
    		$val["ids"] =  $v['ids'];//当前流程ID
    		$val["processname"] = $v['processname']?$v['processname']:'';//节点名称
    		$val["processuser"] = $v['processuser']?$v['processuser']:'';//节点用户ID
    		$val["flag"] = $v['flag']; //节点标签的颜色1-7颜色值
    		$val["modelname"] = $v['modelname'];//模型名称
    		$val["level"] = $v['level'];// 等级
    		$val["prcsid"] = $v['prcsid'];// 节点序号
    		$val["setleft"] = $v['setleft']?$v['setleft']:0;//节点高度
    		$val["settop"] = $v['settop']?$v['settop']:0;//节点位置
    		$val["processaudit"] = $v['processaudit']?$v['processaudit']:'';
    		$val["processto"] = $v['processto']?$v['processto']:'';//下级节点
    		$val["processall"] = $v['processall']?$v['processall']:'';//所有下级节点
    		$val["processcondition"] = $v['processcondition']?$v['processcondition']:'';//判定节点中文显示条件
    		$val["processrules"] = $v['processrules']?$v['processrules']:'';//判定节点sql形式条件
    		$val["processrulesinfo"] = $v['processrulesinfo']?$v['processrulesinfo']:'';//判定节点数组形式条件
    		$val["choose"] = $v['choose']?$v['choose']:0;//0开始节点1判定节点2审批节点3子流程节点
    		$val["filterwritsetempty"] = $v['filterwritsetempty']?$v['filterwritsetempty']:'';//不可修改字段
    		$val["filterreadsetempty"] = $v['filterreadsetempty']?$v['filterreadsetempty']:'';//不可查看字段
    		$val["isauditmodel"] = $v['isauditmodel']?$v['isauditmodel']:'';//子流程模板
    		$val["issourcemodel"] = $v['issourcemodel']?$v['issourcemodel']:'';//子流程漫游来源
    		$val["roamid"] = $v['roamid']?$v['roamid']:'';//漫游配置ID
    		$val['istuihui']= $v['istuihui']?$v['istuihui']:0; //退回节点
    		
    		//知会信息  知会人
    		$val["informpersonid"] = $v['informpersonid']?$v['informpersonid']:'';//知会人
    		
    		if(in_array($v['ids'], $isComplete)){
    			$v['iscomplete'] = 1;
    		}
    		$val["iscomplete"] = $v['iscomplete']?$v['iscomplete']:0;//判定节点数组形式条件
    		$keys = strval($n+1);
    		$n ++;
    		$jsondata["data"]["list"][$keys] = $val;
    	}
    	$jsondata["connectkey"] = "lx".time();
    	if(!empty($jsondata["data"]["list"])){
    		$jsondata["condition"] = array();
    		//判定节点的分支条件数据拼装
    		foreach ($jsondata["data"]["list"] as $flow_val) {
    			if($flow_val['choose'] == 1){ //在判定节点才存在条件
    				$node_config	= trim($flow_val['processto']);
    				$node_condition	= trim($flow_val['processcondition']);
    				if(sizeof($node_config) && sizeof($node_condition)){
    					$arr_node_config	= array_filter(explode(',', $node_config));
    					$arr_node_condition	= explode('#@#', $node_condition);
    					foreach($arr_node_config as $conf_key=>$conf_val){
    						if($arr_node_condition[$conf_key]){
    							$jsondata["condition"][$flow_val['prcsid']."lx".time().'-'.$conf_val."lx".time()] = $arr_node_condition[$conf_key];
    						}
    					}
    				}
    			}
    		}
    	}
    	return json_encode($jsondata);
    }
    /**
     * @Title: getProcessTree
     * @Description: todo(构造左侧流程树)
     * @author 杨东
     * @date 2014-1-8 上午9:47:17
     * @throws
     */
      public function getProcessTree(){
    	//调用节点树
    	$NodeModel=D("Node");
    	$typeTree=$NodeModel->getNodeTree("isprocess=1 and level=3");
    	return json_encode($typeTree);
    }
}
?>