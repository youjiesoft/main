<?php
/**
 * @Title: MisTaxGroupAction
 * @Package 财务配置-税金组信息：功能类
 * @Description: TODO(税金组的记录及维护)
 * @author yangxi
 * @company 重庆特米洛科技有限公司˾
 * @copyright 重庆特米洛科技有限公司˾
 * @date 2013-1-10 19:18:54
 * @version V1.0
 */
class MisTaxGroupAction extends CommonAction{
	/**
	 * @Title: _filter
	 * @Description: todo(重写CommonAction的_filter方法，传递过滤参数后返回列表页面)
	 * @return string
	 * @author yangxi
	 * @date 2013-5-31 下午3:59:44
	 * @throws
	 */	
    public function _filter(&$map){
        if(empty($_REQUEST['status'])) {
		    if ($_SESSION["a"] != 1)$map['status']=array("gt",-1);
		}
    }
	/**
	 * @Title: _before_edit
	 * @Description: todo(edit页面前传入数据)
	 * @return string
	 * @author 杨希
	 * @date 2013-5-31 下午3:59:44
	 * @throws
	 */
    public function _before_edit(){
    		$id=$_GET['id'];//获取到对应需要修改的单据ID
         //实例化对应数据表模型
            $TaxGroup   =D("MisTaxGroup");
         //赋值数据表模型
            $TaxGroupInfo    =$TaxGroup->where("id='".$id."'")->find();
         //给模板赋值
            $this->assign('TaxGroupInfo',$TaxGroupInfo);
    }
}
?>