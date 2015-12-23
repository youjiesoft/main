<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(模板配置流程) 
 * @author liminggang 
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2014-8-15 上午11:11:38 
 * @version V1.0
 */
class MisSystemFlowFormAction extends CommonAction {
	
	public function _filter(&$map){
		//查询是节点类型的数据
		$map['outlinelevel'] = 3;
		if ($_SESSION["a"] != 1)
			$map['status']=array("gt",-1);
	}
	private function getType(){
		//获取流程类型 组合成树结构
		$MisSystemFlowTypeDao = M("mis_system_flow_type");
		$where['status'] = 1;
		$typelist=$MisSystemFlowTypeDao->where($where)->order("sort,id asc")->select();
		//配置树形结构的url请求地址，和div刷新区域
		$paeam['url'] = "__URL__/index/jump/jump/pid/#id#/tid/#parentid#";
		$paeam['rel'] = "MisSystemFlowForm_left";
 		//生成树形结构的json值
		$json_arr=$this->getTree($typelist,$paeam);
		$this->assign('json_arr',$json_arr);
		return $typelist;
	}
	
	public function index(){
		//获取流程类型
		$typelist=$this->getType();
		
		$name = $this->getActionName();
		
		//列表过滤器，生成查询Map对象
		$map = $this->_search ($name);
		
		$pid = $_REQUEST['pid'] = $_REQUEST['pid']?$_REQUEST['pid']:$typelist[0]['id'];
		$this->assign("pid",$pid);
		if($pid){
			$categoryarr = $this->downAllChildren($typelist,$pid);
			$map['category'] = array(' in ',$categoryarr);
		}
		
		if($_REQUEST['tid'] == 0){
			$tid = $pid;
		}else{
			$tid=$_REQUEST['tid']?$_REQUEST['tid']:$typelist[0]['id'];
		}
		$this->assign("tid",$tid);
		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		
		if (! empty ( $name )) {
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
		if($_REQUEST['jump'] == "jump"){
			$this->display('indexview');
		}else{
			$this->display();
		}
	}
	
	function _before_add(){
		//获取流程类型 组合成树结构
		$MisSystemFlowTypeDao = M("mis_system_flow_type");
		$where['status'] = 1;
		$where['outlinelevel'] = 1;
		$ty['a']=$MisSystemFlowTypeDao->where($where)->field("id,name")->select();
		$where = array();
		$where['status'] = 1;
		$where['outlinelevel'] = 2;
		$ty['b']=$MisSystemFlowTypeDao->where($where)->field("id,name,parentid")->select();
		$this->assign("typelist",json_encode($ty));
	}
	
	/**
	 * @Title: _before_edit
	 * @Description: todo(前置编辑函数)
	 * @author 屈强
	 * @date 2014-08-15 10:51:06
	 * @throws 
	*/
	function _before_edit(){
		//获取流程类型 组合成树结构
		$MisSystemFlowTypeDao = M("mis_system_flow_type");
		$where['status'] = 1;
		$where['outlinelevel'] = 1;
		$ty['a']=$MisSystemFlowTypeDao->where($where)->field("id,name")->select();
		$where = array();
		$where['status'] = 1;
		$where['outlinelevel'] = 2;
		$ty['b']=$MisSystemFlowTypeDao->where($where)->field("id,name,parentid")->select();
		$this->assign("typelist",json_encode($ty));
	}
	
	private function getGttType($id){
		//第一步，组合每个类型的计划开始时间，和计划结束时间
		$MisSystemFlowFormDao = M("mis_system_flow_form");
		$where = array();
		$where['supcategory'] = $id;
		$where['status'] = 1; //状态正常
		$formlist=$MisSystemFlowFormDao->where($where)->select();
		
		$list = array();
		$min = $max = array();
		foreach($formlist as $key=>$val){
			if(in_array($val['category'],array_keys($list))){
				//如果在这个数组里面，进行时间的大小比较
				if($list[$val['category']]['start'] > $val['begintime']){
					$list[$val['category']]['start'] = $val['begintime'];
				}
				if($list[$val['category']]['finish'] < $val['endtime']){
					$list[$val['category']]['finish'] = $val['endtime'];
				}
			}else{
				//默认封装第一个
				$a = array("start"=>$val['begintime'],"finish"=>$val['endtime']);
				$list[$val['category']] = $a;
			}
			//封装顶级类型的最小时间
			if($min !=null && $min > $val['begintime']){
				$min = $val['begintime'];
			}
			if($min ==null){
				$min = $val['begintime'];
			}
			//封装顶级类型的最大时间
			if($max !=null && $max < $val['endtime']){
				$max = $val['endtime'];
			}
			if($max == null){
				$max = $val['endtime'];
			}
		}
		$list[$id] = array("start"=>$min,"finish"=>$max);
		return $list;
	}
	
	public function gantt(){
		//获取过来的类型ID
		$id = $_REQUEST['id'];
		$MisSystemFlowTypeDao = M("mis_system_flow_type");
		
		$tlist=$this->getGttType($id);
		$where['id'] = $id;
		$where['parentid'] = $id;
		$where['_logic'] = 'or';
		$typelist=$MisSystemFlowTypeDao->where($where)->select();
		//构建xml字符串
		//初始化xml字符串头
		$str="<Project><Tasks>";
		//组合头信息
		for($i=0;$i<1;$i++){
			$str .= '<Task>';
			$str .= '<UID>'. $i .'</UID>';
			$str .= '<ID>'. $i .'</ID>';
			$str .= '<Name>农业担保产品线</Name>';
			$str .= '<OutlineNumber>'. $i .'</OutlineNumber>';
			$str .= '<Start>'.transTime($tlist[$id]['start'],'Y-m-d H:i:s').'</Start>';
			$str .= '<Finish>'.transTime($tlist[$id]['finish'],'Y-m-d H:i:s').'</Finish>';
			$str .= '<Summary>1</Summary>';
			$str .= '<PercentComplete>0</PercentComplete>';
			$str .= '</Task>';
		}
		//构建第一层甘特图任务
		foreach($typelist as $key=>$val){
			$num = $key;
			$kw = "1.".$num;
			if($num==0){
				$kw=1;
			}
			$key = $key+1;
			$str .= '<Task>';
			$str .= '<UID>'. $key .'</UID>';
			$str .= '<ID>'. $key .'</ID>';
			$str .= '<Name>'.$val['name'].'</Name>';
			$str .= '<CreateDate>'.transTime($val['createtime']).'</CreateDate>';
			$str .= '<Start>'.transTime($tlist[$val['id']]['start'],'Y-m-d H:i:s').'</Start>';
			$str .= '<Finish>'.transTime($tlist[$val['id']]['finish'],'Y-m-d H:i:s').'</Finish>';
			$str .= '<Summary>1</Summary>';  //大类
			$str .= '<OutlineNumber>'.$kw.'</OutlineNumber>';
			$str .= '<PercentComplete>100</PercentComplete>'; //完成比例
			$str .= '<ConstraintType>0</ConstraintType>'; //约束
			$str .= '</Task>';
			if($num!=0){
				$this->getGttXml($val['id'], $kw, 1,$str);
			}
		}
		//添加xml字符串尾
		$str .='</Tasks>';
		$str .='</Project>';
		$this->assign("str",$str);
		//输出到页面信息
		$this->display();
	}
	
	private function getGttXml($id,$key,$type,&$str){
		//获取该项目中的mis_system_flow_form数据
		$MisSystemFlowFomrDao = M("mis_system_flow_form");
		$submap['status'] = 1;
		$submap['category'] = $id;
		
		$sublist = $MisSystemFlowFomrDao->where($submap)->select();
		foreach($sublist as $k=>$v){
			//获取是否还有下一级
			$where = array();
			$where['fid'] = $v['id'];
			$where['status'] = 1;
			$Summary = 0;
			$k = $k+1;
			$a=str_replace(".", "-", $key);
			$ks = $a."-".$k;
			$keys = $key.".".$k;
			$str .= '<Task>';
			$str .= '<UID>'. $ks .'</UID>';
			$str .= '<ID>'. $ks .'</ID>';
			$str .= '<Name>'.$v['name'].'</Name>';
			$str .= '<CreateDate>'.transTime($val['createtime']).'</CreateDate>';
			$str .= '<OutlineNumber>'.$keys.'</OutlineNumber>';
			$str .= '<Start>'.transTime($v['begintime'],'Y-m-d H:i:s').'</Start>';
			$str .= '<Finish>'.transTime($v['endtime'],'Y-m-d H:i:s').'</Finish>';
			$str .= '<Summary>'.$Summary.'</Summary>';
			$str .= '<PercentComplete>0</PercentComplete>';
			$str .= '</Task>';
		}
	}
}
?>