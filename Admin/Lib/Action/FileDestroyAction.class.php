<?php
/**
 * @Title: FileDestroyAction 
 * @Package package_name
 * @Description: todo(销毁文件-动态表单成套文件销毁) 
 * @author quqiang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-11-26 下午06:34:44 
 * @version V1.0
 */
class FileDestroyAction extends CommonAction{
	
	/**
	 * @Title: destroy 
	 * @Description: todo(销毁指定Action名称的所有文件) 
	 * @param string $actionName	  
	 * @author quqiang 
	 * @date 2014-11-26 下午06:36:02 
	 * @throws
	 */
	protected function destroy($actionName){
		/**
		 * 动态表单删除文件流程：
		 * 1：销毁action、model、view、actionExtend、modelExtend、模板文件
		 * 2：销毁动态配置中的
		 */
	}
}