<?php
/**
 * @Title: SystemTypeViewAction
 * @Package 基础配置-全局初始化系统单据：配置文件操作类
 * @Description: TODO(系统单据配置文件的记录及维护)
 * @author yangxi
 * @company 重庆特米洛科技有限公司˾
 * @copyright 重庆特米洛科技有限公司˾
 * @date 2013-1-10 19:18:54
 * @version V1.0
 */
class SystemTypeViewAction extends CommonAction
{
	/**
	 * @Title: index
	 * @Description: todo(重写CommonAction的index方法，展示列表)
	 * @return string
	 * @author 杨希
	 * @date 2013-5-31 下午3:59:44
	 * @throws
	 */
    public function index(){
    	//动态配置列表项字段  包括：1、是否显示；2、是否排序；3、列宽度
		$scdmodel = D('SystemConfigDetail');
		$modelname = $this->getActionName();
		$detailList = $scdmodel->getDetail($modelname);
		if ($detailList) {
			$fieldsarr = array();
			foreach ($detailList as $k => $v) {
				$fieldsarr[$v['name']]['showname'] = $v['showname'];
				$fieldsarr[$v['name']]['sorts'] = $v['sorts'];
				$fieldsarr[$v['name']]['widths'] = $v['widths'];
				$fieldsarr[$v['name']]['shows'] = $v['shows'];
			}
			$this->assign ( 'fieldsarr', $fieldsarr );
		}
		//扩展工具栏操作
		$toolbarextension = $scdmodel->getDetail($modelname,true,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
        $model=D('SystemTypeView');
        if(file_exists($model->GetFile())){
            require $model->GetFile();
        }
        //重写index方法需要重新实例化动态配置
        $scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($name);
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		//
        $this->assign("list",$aryType);
        $this->display();
    }

    /**
     * @Title: update
     * @Description: todo(重写CommonAction的update方法，更新)
     * @return string
     * @author 马世河
     * @date 2013-5-31 下午3:59:44
     * @throws
     */
    public function  update() {
        $model=D('SystemTypeView');
        $data=array(
            'type'=>$_REQUEST['type'],
            'typename'=>$_REQUEST['typename'],
            'modelname'=>$_REQUEST['modelname'],
            'status'=>$_REQUEST['status']
        );
        if(file_exists($model->GetFile())){
            require $model->GetFile();
        }
        $aryType[$_REQUEST['type']]=$data;
        $model->SetType($aryType);
        $this->success ( L('_SUCCESS_') );
    }
}
?>