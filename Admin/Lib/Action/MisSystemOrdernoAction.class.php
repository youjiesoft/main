<?php
/**
 * @Title: MisSystemOrdernoAction 
 * @Package package_name
 * @Description: 基础档案编码规则
 * @author 黎明刚 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014年11月3日 上午9:52:27 
 * @version V1.0
 */
class MisSystemOrdernoAction extends CommonAction {
	public function index() {
		$name = $this->getActionName ();
		$scdmodel = D ( 'SystemConfigDetail' );
		$detailList = $scdmodel->getDetail ( $name );
		$this->assign ( 'detailList', $detailList );
		//根据node节点来控制是否删除了基础编码方案
		$NodeDao = M("node");
		$where['level'] = 3;
		$where['status'] =1;
		$nodelist = $NodeDao->where($where)->getField("id,name");
		
		$model = D ( $name );
		//查询基础档案编码方案
		$where = array();
		$where['tablename'] = array(' in ',$nodelist);
		$volist = $model->where($where)->select ();
		$this->assign ( "volist", $volist );
		$this->display ();
	}
	public function update() {
		$name = $this->getActionName();
		$model = D($name);
		$tablename = $_POST ['tablename'];
		foreach($tablename as $key=>$val){
			$data = array();
			$data['one'] = $_POST['one'][$val];
			$data['two'] = $_POST['two'][$val];
			$data['three'] = $_POST['three'][$val];
			$data['four'] = $_POST['four'][$val];
			$data['five'] = $_POST['five'][$val];
			$data['six'] = $_POST['six'][$val];
			$data['seven'] = $_POST['seven'][$val];
			$data['eight'] = $_POST['eight'][$val];
			$data['nine'] = $_POST['nine'][$val];
			$data['ten'] = $_POST['ten'][$val];
			$data['eleven'] = $_POST['eleven'][$val];
			$data['twelve'] = $_POST['twelve'][$val];
			$data['thirteen'] = $_POST['thirteen'][$val];
			$where = array();
			$where['tablename'] = $val;
			$result = $model->where($where)->save($data);
			if(!$result){
				$this->error("编码方案修改失败");
			}
		}
		$this->success("编码方案修改成功");
	}
}
?>