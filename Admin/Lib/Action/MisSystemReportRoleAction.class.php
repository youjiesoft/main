<?php
/**
 * @Title: MisSystemPanelRoleAction
 * @Package 基础配置-面板管理：系统面板授权
 * @Description: TODO()
 * @author jiangx
 * @company 重庆特米洛科技有限公司
 * @copyright 重庆特米洛科技有限公司
 * @date 2013-10-10 16:18:54
 * @version V1.0
 */
class MisSystemReportRoleAction extends CommonAction {
    /**
    * @Title: _before_index 
    * @Description: todo(打开页面前置函数)
    * @author jiangx 
    * @date 2013-10-11
    * @throws
    */
    public function _before_index(){
        $roletype = array(
            '-1' => array(
                'name' => '权限菜单',
            ),
            '1' => array(
                'name' => '用户授权',
                'md' => 'MisSystemReportRole',
            ),
			'2' => array(
                'name' => '部门授权',
                'md' => 'MisSystemReportDepartmentRole',
            ),
        );
        $roletree = array();
        foreach ($roletype as $key => $val) {
            $new = array();
            $new['id'] = $key;
            $new['pId'] = $key == -1 ? 0 : -1;
            $new['title'] = $val['name']; //光标提示信息
            $new['name'] = $val['name']; //结点名字，太多会被截取
            if ($key != -1) {
                $new['url']="__URL__/index/frame/1/md/". $val['md'];
                $new['target']='ajax';
                $new['rel']="missystemreportroleBox";
            }
            
            
            $new['open'] = 'true';
            $roletree[] = $new;
        }
        $this->assign('roleTree', json_encode($roletree));
    }
    /**
    * @Title: index 
    * @Description: todo(重写index)
    * @author jiangx 
    * @date 2013-10-11
    * @throws
    */
    public function index(){
		$map = array();
		$map['status'] = 1;
		if (!$_REQUEST['md']) {
			$_REQUEST['md'] = 'MisSystemReportRole';
		}
		if ($_REQUEST['md'] == 'MisSystemReportRole') {
			$map = $this->_search ();
			$name = $_REQUEST['md'];
			$this->_list ( $name, $map );
			//searchby搜索扩展
			$scdmodel = D('SystemConfigDetail');
			$detailList = $scdmodel->getDetail($name);
			if ($detailList) {
				$this->assign ( 'detailList', $detailList );
			}
			$searchby = $scdmodel->getDetail($name,true,'searchby');
			if ($searchby && $detailList) {
				$searchbylist=array();
				foreach( $detailList as $k=>$v ){
					if(isset($searchby[$v['name']])){
						$arr['id']= $searchby[$v['name']]['field'];
						$arr['val']= $v['showname'];
						array_push($searchbylist,$arr);
					}
				}
				$this->assign("searchbylist",$searchbylist);
			}
		}
		if ($_REQUEST['md'] == 'MisSystemReportDepartmentRole') {
			$departmentModel = D("MisSystemDepartment");
			
			$departmentlist = $departmentModel->where($map)->select();
			$parent = $departmentModel->where('parentid = 0 AND status = 1')->find();
			$arraylist = array();
			$arraylist[] = $parent;
			$i = 1;
			foreach ($departmentlist as $key => $val) {
				$str = "";
				if ($val['parentid'] == $parent['id']) {
					for ($j = $i; $j >0; $j--) {
						$str .= "——";
					}
					$val['name'] = $str .'&nbsp;'. $val['name'];
					$arraylist[] = $val;
					unset($departmentlist[$key]);
					$this->getDepartmentTree(&$arraylist, &$departmentlist, $val['id'], $i);
				}
			}
			$this->assign('list', $arraylist);
		}
		$this->assign('md', $_REQUEST['md']);
		if ($_REQUEST['frame']) {
			if ($_REQUEST['md'] == "MisSystemReportRole") {
				$this->display('panelroleuser');exit;
			} else {
				$this->display('panelroledepartment');exit;
			}
		} else {
			$this->display();
		}
    }
	/**
    * @Title: getDepartmentTree 
    * @Description: todo(取得部门顺序树形)
    * @author jiangx 
    * @date 2013-10-18
    * @throws
    */
	private function getDepartmentTree($arraylist, $voList, $parentid, $i){
		$i ++ ;
		foreach ($voList as $key => $val) {
			$str = "";
			if ($parentid == $val['parentid']) {
				for ($j = $i; $j >0; $j--) {
					$str .= "——";
				}
				$val['name'] = $str .'&nbsp;'. $val['name'];
				$arraylist[] = $val;
				unset($voList[$key]);
				$this->getDepartmentTree(&$arraylist, &$voList, $val['id'], $i);
			}
		}
	}
    /**
    * @Title: setPanelRoleUser 
    * @Description: todo(加载用户权限页面)
    * @author jiangx 
    * @date 2013-10-11
    * @throws
    */
    public function getPanelRoleList(){
    	
		$this->assign('type', $_REQUEST['type']);
        $this->assign('partid', $_REQUEST['partid']);
        //获取报表面板的数据
		$panenmodel = D("MisSystemReport");
        $map = array();
        $map['status'] = 1;
        $panellist = $panenmodel->where($map)->getField('id,name,modelname,isbasetab');
        //获取用户/部门权限
		$rolelist = "";
		$map = array();
		$map['status'] = 1;
		if ($_REQUEST['type'] == 1) {
			$rolemodel = D("MisSystemReportUserRole");
			$map['userid'] = $_REQUEST['partid'];
		} else {
			$rolemodel = D("MisSystemReportDepartmentRole");
			$map['departmentid'] = $_REQUEST['partid'];
		}
		
		$rolelist = $rolemodel->where($map)->find();
        $this->assign('rolelist', $rolelist);
        //面板是否具有权限
        if ($rolelist['panlerole']) {
            $userrolearr = explode(',', $rolelist['panlerole']);
            foreach ($userrolearr as $val) {
                $panellist[$val]['isrole'] = 1;
            }
        }
        //基础面板权限默认都有  动态报表面板需要排除基础报表面板
		$aBasepanel = array();
		foreach ($panellist as $key => $val){
			if ($val['isbasetab'] == 1) {
				if (!$rolelist) {
					$panellist[$key]['isrole'] = 1;
				}
				$aBasepanel[] = $panellist[$key];
				unset($panellist[$key]);
			}
		}
		$this->assign('aBasepanel', $aBasepanel);
        $this->assign('panellist', $panellist);
        $this->display();
    }
    /**
    * @Title: saveSetPanelRoleUser 
    * @Description: todo(保存用户权限数据)
    * @author jiangx 
    * @date 2013-10-11
    * @throws
    */
    public function saveSetPanelRoleUser(){
        if ($_POST['panlerole']) {
            $_POST['panlerole'] = implode(',', $_POST['panlerole']);
        } else {
			$_POST['panlerole'] = "";
		}
		if ($_REQUEST['type'] == 1) {
			$model = D("MisSystemReportUserRole");
			$_POST['userid'] = $_POST['partid'];
		} else {
			$model = D("MisSystemReportDepartmentRole");
			$_POST['departmentid'] = $_POST['partid'];
		}
        
        if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
        if ($_POST['id']) {
            $list=$model->save ();
        } else {
            $list=$model->add ();
        }
		if ($list === false) {
            $this->error ( L('_ERROR_') );
        }
        $this->success ( L('_SUCCESS_') );
    }
}
?>