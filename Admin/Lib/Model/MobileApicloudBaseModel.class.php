<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(推送内) 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-7-2 下午5:16:22 
 * @version V1.0
 */
class MobileApicloudBaseModel extends CommonModel {
	protected $trueTableName = 'mis_system_app_tuisong';
	/**
	 * 
	 * @Title: getTuisong
	 * @Description: todo(手机APP推送信息池) 
	 * @param 推送标题 $title
	 * @param 推送人 $userIds 多人的时候用逗号分割  1,2,3
	 * @param 推送类型 $type  1为货主端 2为司机端
	 * @param 推送内容 $content 推送内容可以能为一段url地址，表示打开APP那个页面
	 * @param 触发时间 $executetime 默认可以不传入 该参数传入后。表示当前时间大于这个时间时，才会触发推送,及超时推送
	 * @return boolean  
	 * @author liuzhihong 
	 * @date 2016年3月26日 下午12:55:59 
	 * @throws
	 */
	public function getTuisong($title,$userIds,$type,$content="内容",$executetime){
		$data["title"] = $title;//推送内容
		$data["content"] = $content; //这里要默认
		$data["userIds"] = $userIds;//推送给的ID 如果要推送给多个人 则用, 隔开  写法 6,7 则推送给ID为6 7的两个人 
		$data["type"] = $type; //推送给那个端 1为货主端 0为司机段
		$data["status"] = 1;//状态为 未推送
		$data["createtime"] = time();
		$data["executetime"] = $executetime?$executetime:0;
		$result = M("mis_system_app_tuisong")->add($data);
		if($result===false){
			return false;
		}else{
			return true;
		}
	}
}
?>