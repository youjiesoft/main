<?php
/**
 * @Title: CommonAuditAction
 * @Package package_name
 * @Description: todo(报表公共处理器)
 * @author renling
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2013-12-18 上午11:30:41
 * @version V1.0
 */
class CommonReportAction extends CommonAction{
	/**
	 *
	 * @Title: _listData
	 * @Description: todo(数据列表)
	 * @param unknown_type modelname
	 * @param unknown_type sql条件
	 * @param unknown_type 排序字段(id asc)
	 * @param unknown_type 当前页数
	 * @param unknown_type 从$limit开始取数据
	 * @param 对应值(数组) $rowField array('fbid','customername');
	 * @param  $iscolModel 导出excel为false 封装列表为true
	 * @param unknown_type 特殊处理 控制器名称
	 * @return unknown|string
	 * @author renling
	 * @date 2013-12-18 下午6:00:26
	 * @throws
	 */
	protected function _listData($name, $whereSql, $sortBy,$page,$limit,$rowField,$iscolModel=true,$aname='') {
		$model = D($name);
		//查询符合条件总条数
		$count = $model->where ($whereSql)->count ('*');
// 		echo $model->getLastSql();
		if( $count > 0 ) {
			$total_pages = ceil($count/$limit);
		} else {
			$total_pages = 0;
		}
		if ($page > $total_pages) $page = $total_pages;
		$start = $limit*$page - $limit;
		if ($start<0) $start = 0;
		if( $count > 0 ) {
			//查询符合条件的数据
			if($iscolModel==false){
				$voList = $model->where($whereSql)->order($sortBy)->select();
			}else{
				$voList = $model->where($whereSql)->order($sortBy)->limit($start.','.$limit)->select();
			}
			$module = A($aname);
			if (method_exists($module,"_after_listData")) {
					//便于导出excel特殊处理参数
					call_user_func(array(&$module,"_after_listData"),&$voList,&$iscolModel);
			}
		}
		if($iscolModel==false){
			return $voList;
		}else{
			$responce->page = $page;
			$responce->total = $total_pages;
			$responce->records = $count;
			$i = 0;
			//根据$rowField封装colmodel
			foreach($voList as $k => $row){
				$cell = array();
				foreach ($rowField as $key => $value) {
					$cell[$key] = $row[$value];
				}
				$responce->rows[$i]['cell'] =$cell;
				$i++;
			}
			return json_encode($responce);
		}
	}
}
?>