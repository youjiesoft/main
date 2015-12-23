<?php
/**
 * @Title: MisSalesSiteAction
 * @Package 基础配置-区域相关信息：功能类
 * @Description: TODO(区域相关的记录及维护)
 * @author yangxi
 * @company 重庆特米洛科技有限公司˾
 * @copyright 重庆特米洛科技有限公司˾
 * @date 2013-1-10 19:18:54
 * @version V1.0
 */
class MisSalesSiteAction extends CommonAction
{
	/**
	 * @Title: _filter
	 * @Description: todo(重写CommonAction的_filter方法，传递过滤参数后返回列表页面)
	 * @return string
	 * @author yangxi
	 * @date 2013-5-31 下午3:59:44
	 * @throws
	 */	
	public function _filter(&$map) {
		 if ($_SESSION["a"] != 1)$map['status']=array("gt",-1);
	}
	
	public function index(){
		$name=$this->getActionName();
		$this->getSystemConfigDetail($name);
		$model=D($name);
		//查询当前所有行业类型
		$list=$model->where('status = 1')->select();
		//封装行业类型结构树
		$listnew = $list;
		foreach($listnew as $k=>$v){
			$listnew[$k]['name'] = "(".$v['orderno'].")".$v['name'];
		}
		$param['rel']="MisSalesSiteview";
		$param['url']="__URL__/index/jump/1/qy/#id#";
		$treemiso[]=array(
				'id'=>0,
				'pId'=>0,
				'name'=>'区域类别',
				'title'=>'区域类别',
				'open'=>true,
				'isParent'=>true,
		);
		$treearr = $this->getTree($listnew,$param,$treemiso,false);
		$this->assign("treearr",$treearr);
		//获取行业ID
		$qy = $_REQUEST['qy'];
		//定义一个存储行业数据数组
		$vo = array();
		if($qy){
			$map['id'] = $qy;
			$vo=$model->where($map)->find();
		}else{
			if($list){
				//判断是否存在行业
				$vo = $list[0];
				$qy = $list[0]['id'];
			}
		}
		$this->assign("valid",$qy);
		$this->assign("vo",$vo);
	
		if($_REQUEST['jump'] == 1){
			$this->display("indexview");
		}else{
			$this->display();
		}
	}
	/**
	 * @Title: _before_insert
	 * @Description: 插入之前，验证编码方案是否正确符合规定
	 * @author 黎明刚
	 * @date 2014年11月3日 上午11:03:07
	 * @throws
	 */
	function _before_insert(){
		$name = $this->getActionName();
		$orderno = $_POST['orderno'];
		$MisSystemOrdernoDao = D("MisSystemOrderno");
		$data = $MisSystemOrdernoDao->validateOrderno($name,$orderno);
		if($data['result']){
			$_POST['parentid'] = $data['parentid'];
		}else{
			$this->error($data['altMsg']);
		}
	}
	public function _before_update(){
		$name = $this->getActionName();
		$orderno = $_POST['orderno'];
		$MisSystemOrdernoDao = D("MisSystemOrderno");
		$data = $MisSystemOrdernoDao->validateOrderno($name,$orderno,$_POST['id']);
		if($data['result']){
			$_POST['parentid'] = $data['parentid'];
		}else{
			$this->error($data['altMsg']);
		}
	}
	public function _before_delete(){
		$name = $this->getActionName();
		$map['parentid'] = $_REQUEST['id'];
		$map['status'] = 1;
		$model = D($name);
		$data = $model->where($map)->select();
		if($data){
			$this->error('此类下面有分类，不能删除');
		}
	}
	
}
?>