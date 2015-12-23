<?php
//Version 1.0
/**
 * @Title: MisSystemDataAccessModel 
 * @Package package_name
 * @Description: todo(数据权限控制) 
 * @author谢友志 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015-6-1 下午2:59:13 
 * @version V1.0
 */
class MisSystemDataAccessCarrierModel extends CommonModel{
    protected $trueTableName = 'mis_system_data_access_sub';
    
//     public $_validate	=	array(
//            array('masid,objid,objtype','','目标对象已对该字段做过权限分配！',self::MUST_VALIDATE,'unique',self::MODEL_INSERT),//多字段组合验证
//     );
    public function getCompanyOne(){
    	$companyid=$this->where("status=1")->getField("id");
    	return $companyid;
    }
    /**
     * @Title: userAndRolegroupToTree
     * @Description: todo(包含人员和角色的左侧树数组) 
     * @param unknown_type $rel  
     * @author 谢友志 
     * @date 2015-6-8 下午1:36:57 
     * @throws
     */
   public function userAndRolegroupToTree($rel){
	   	$usermodel = M("user");
	   	$rolegroupmodel = M("rolegroup");
	   	$companyModel = M("mis_system_company");
	   	$companylist = $companyModel->where("status=1")->getField("id,name");
	   	$userlist = $usermodel->field("id,name,companyid")->where("status=1")->select();
	   	$rolegrouplist = $rolegroupmodel->field("id,name,companyid")->where("status=1 and catgory=1")->select();
		//echo $rolegroupmodel->getlastsql();
	   	$newv = array();
	   	$new = array();
	   	foreach($userlist as $k=>$v){
	   		$newv[$k]['id']=$v['id'];
	   		$newv[$k]['pId']=-1;
	   		$newv[$k]['type']='post';
	   		$newv[$k]['url']=__URL__."/index/jump/jump/type/1/id/".$v['id'];
	   		$newv[$k]['target']='ajax';
	   		$newv[$k]['rel']=$rel;
	   		$newv[$k]['title']=$v['name']; //光标提示信息
	   		$newv[$k]['name']=$v['name']; //结点名字，太多会被截取,针对于现在的ZTREE，宽度不能超过18个字符。
	   		$newv[$k]['open']=false;
	   		$newv[$k]['isParent'] = false;
	   	}
	   	foreach($rolegrouplist as $k=>$v){
	   		$new[$k]['id']="R_".$v['id'];
	   		$new[$k]['pId']=-2;
	   		$new[$k]['type']='post';
	   		$new[$k]['url']=__URL__."/index/jump/jump/type/2/id/".$v['id'];
	   		$new[$k]['target']='ajax';
	   		$new[$k]['rel']=$rel;
	   		$new[$k]['title']=$v['name']; //光标提示信息
	   		$new[$k]['name']=$v['name']; //结点名字，太多会被截取,针对于现在的ZTREE，宽度不能超过18个字符。
	   		$new[$k]['open']=false;
	   		$new[$k]['isParent'] = false;
	   	}
	   	$ztree[]=array(
	   			'id'=>-1,
	   			'pId'=>-3,
	   			'title'=>'人员', //光标提示信息
	   			'name'=>'人员',
	   			'open'=>false,
	   			'isParent' => true,
	   	);
	   	$ztree[]=array(
	   			'id'=>-2,
	   			'pId'=>-3,
	   			'title'=>'角色', //光标提示信息
	   			'name'=>'角色',
	   			'open'=>false,
	   			'isParent' => true,
	   	);
	   	$ztree[]=array(
	   			'id'=>-3,
	   			'pId'=>"0",
	   			'title'=>'对象', //光标提示信息
	   			'name'=>'对象',
	   			'open'=>true,
	   			'isParent' => true,
	   	);
	   	
	   	$typeTree = array_merge($ztree,$newv,$new);
	   	return $typeTree;
   }
}
?>