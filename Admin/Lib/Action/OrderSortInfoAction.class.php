<?php
/**
 * @Title: OrderSortInfoAction
 * @Package 节点排序：功能类
 * @Description: TODO(节点排序)
 * @author jiangx
 * @company 重庆特米洛科技有限公司˾
 * @copyright 重庆特米洛科技有限公司˾
 * @date 2013-6-06
 * @version V1.0
 */ 
class OrderSortInfoAction extends CommonAction {
    /**
     * @Title: _filter 
     * @Description: todo(构造检索条件) 
     * @param  $map  
     * @author jiangx
     * @date 
     * @throws 
    */  
    public function _filter(&$map){
        $searchby = $_POST["searchby"];
        $searchtype = $_POST["searchtype"];
        $keyword = $_POST['keyword'];
        if ($keyword) {
            $map[$searchby] = ($searchtype==2)  ? array('like','%'.$keyword.'%'):$keyword;
            $this->assign("keyword",$keyword);
            if ($_SESSION["a"] != 1) {
                $map['status']=array("gt",-1);
            }
            $model = D("Node");
            $resultlist = $model->where($map)->getField('id,name,title');
            unset($map[$searchby]);
            //判断是否存在子节点
            foreach ($resultlist as $key=>$val) {
                $map['pid'] = $key;
                $count = $model->where ( $map )->count ( '*' );
                if($count <= 0){
                    unset($resultlist[$key]);
                }
            }
            $this->assign('resultlist',$resultlist);
        }
       
        $this->assign("searchtype",$searchtype);
        $this->assign("searchby",$searchby);
    }
    /**
     * @Title: index 
     * @Description: todo(重写index) 
     * @author jiangx
     * @date 
     * @throws 
    */ 
    public function index(){
        $map = array();
        if(method_exists($this,'_filter')){
            $this->_filter($map);
        }
        $id = $_REQUEST['id'];
        if ($id) {
            $model = D("Node");
            $map = array();
            $map['pid'] = $id;
            if ($_SESSION["a"] != 1) {
                $map['status']=array("gt",-1);
            }
            $nodelist = $model->where($map)->order('sort ASC')->select();
            $this->assign('volist',$nodelist);
        }
        $searchby=array(
            array("id" =>"name","val"=>"名称"),
            array("id" =>"title","val"=>"中文名"),
        );
        $searchtype=array(
            array("id" =>"2","val"=>"模糊查找"),
            array("id" =>"1","val"=>"精确查找")
        );
        $this->assign("searchbylist",$searchby);
        $this->assign("searchtypelist",$searchtype);
        $this->display();
    }
    /**
     * @Title: update 
     * @Description: todo(重写update) 
     * @author jiangx
     * @date 
     * @throws 
    */
    public function update(){
        $model = D("Node");
        $orderlist = $_POST['order'];
        $i = 1;
        foreach ($orderlist as $key => $val) {
            $result = $model->where('id='.$val)->setField('sort',$i);
            if ($result === false) {
                $this->error ( L('_ERROR_') );
            }
            $i++;
        }
        $this->success ( L('_SUCCESS_'));
    }
    /**
     * @Title: selectToOrderSort 
     * @Description: todo(排序)
     * @author jiangx
     * @date 2013-08-05
     * @throws 
    */
    public function selectToOrderSort(){
        if (!$_REQUEST['tablename']) {
            $this->error ( "请刷新页面后在排序" );
        }
        //获取刷新页面数据 如“navTabId/MisImportExceledit”
        if ($_REQUEST['refresh']) {
            $refresh = explode('-', $_REQUEST['refresh']);
            $this->assign('refresh', $refresh);
        }
        //获取该表的所有字段
        $database = C ('DB_NAME');
        $tablename = parse_name($_REQUEST['tablename'], 0);
        $tablemodel=M("INFORMATION_SCHEMA.COLUMNS","","",1);
        $fieldlist = $tablemodel->where("table_name = '".$tablename."' AND TABLE_SCHEMA = '".C('DB_NAME')."'")->getField("COLUMN_NAME,COLUMN_COMMENT");
        if (!isset($fieldlist['sort'])) {
            $this->error ( "改表".$tablename."中没有排序字段，请检查" );
        }
        $model = D(parse_name($_REQUEST['tablename'], 1));
        $map = array();
        //组装查询语句
        if ($_REQUEST['where']) {
            $where = explode('-', $_REQUEST['where']);
            foreach ($where as $key => $val) {
                if (!$val) {
                    continue;
                }
                $subwhere = explode(':', $val);
                if (count($subwhere) > 2) {
                    $map[$subwhere[0]] = array($subwhere[1], $subwhere[2]);
                } else {
                   $map[$subwhere[0]] = $subwhere[1]; 
                }
            }
        }
        if (isset($fieldlist['status'])) {
            $map['status'] = 1;
        }
        if ($map) {
            $list = $model->where($map)->order('sort')->select();
        } else {
            $list = $model->order('sort')->select();
        }
        foreach ($list as $key => $val) {
            if ($val['name']) {
                break;
            }
            if ($val['title']) {
                $list[$key]['name'] = $val['title'];
            }
        }
        $this->assign('fieldlist', $list);
        $this->assign('tablename', $_REQUEST['tablename']);
        $this->display('selectToOrderSort');
    }
    /**
     * @Title: saveSelectToOrderSort 
     * @Description: todo(保存排序)
     * @author jiangx
     * @date 2013-08-05
     * @throws 
    */
    public function saveSelectToOrderSort(){
        $model = D(parse_name($_REQUEST['tablename'], 1));
        if (!$_POST['orderbyid']) {
            $this->error ('没数据，请检查');
        }
        foreach ($_POST['orderbyid'] as $key => $val) {
            $result = $model->where('id='.$val)->setField('sort',$key+1);
            if ($result === false) {
                $this->error ( L('_ERROR_') );
            }
        }
        $this->success ( L('_SUCCESS_'));
    }
}
?>