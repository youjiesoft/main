<?php
/***********************************************************
    [EasyTalk] (C)2009 - 2011 laoli.me
    This is NOT a freeware, use is subject to license terms

    @Filename sendsms_action.class.php $

    @Author laoli $

    @Date 2011-04-16 08:45:20 $
*************************************************************/

class friendlink_action {
    var $pm;

    function __construct(&$pluginManager,$action) {
        $this->pm=$pluginManager;
        if (method_exists($this->pm,add_view)) {
            if ($action=='view') {
                $this->showlink();
            }
        }
    }

    //侧边栏图标
    function showlink()  {
        $_site=F('site');
        $pos=json_decode($_site['flinkpos'],true);
        if (in_array('pub',$pos)) {
            $this->pm->add_view('pub_side_bottom','pub',$this,'linkdata');
        }
        if (in_array('home',$pos)) {
            $this->pm->add_view('home_side_bottom','home',$this,'linkdata');
        }
        if (in_array('profile',$pos)) {
            $this->pm->add_view('profile_side_bottom','profile',$this,'linkdata');
        }
    }

    public function linkdata() {
        $linkdata=include(ET_ROOT.'/Home/Runtime/Data/friendlink.php');
        if ($linkdata) {
            $imglink=$txtlink=array();
            foreach ($linkdata as $val) {
                if ($val['logourl']) {
                    $imglink[]=$val;
                } else {
                    $txtlink[]=$val;
                }
            }
            $res.='<style>
            .plugin_friendlink{list-style:none;*zoom:1}
            .plugin_friendlink li{margin:5px 0px;}
            #sidebar .plugin_friendlink img{width:expression(this.width > 170 ? "170px":true);max-width:170px;height:expression(this.height > 170 ? "170px":true);max-height:170px;border:1px solid #999999}
            .plugin_friendlink .txtlink{border-bottom:dashed 1px #cccccc;padding-bottom:5px;margin-right:10px}
            </style>';

            $res.='<div class="sect ">
            <h2>友情链接</h2>
            <ul class="plugin_friendlink">';
            if ($imglink) {
                foreach ($imglink as $val) {
                    $res.='<li><a href="'.$val['linkurl'].'" title="'.$val['name'].'" target="_blank"><img src="'.$val['logourl'].'" alt="'.$val['name'].'"></a></li>';
                }
            }
            if ($txtlink) {
                foreach ($txtlink as $val) {
                    $res.='<li class="txtlink"><a href="'.$val['linkurl'].'" target="_blank">'.$val['name'].'</a></li>';
                }
            }
            $res.='</ul>
            </div>';

            return $res;
        } else {
            $linkdata = D('Friendlink')->select();
            F('friendlink',$linkdata,'./Home/Runtime/Data/');
            return'';
        }
    }

    //插件安装
    public function install() {
        $model=new model();
        $model->query("CREATE TABLE IF NOT EXISTS `".C('DB_PREFIX')."friendlink` (
          `id` int(10) NOT NULL auto_increment,
          `name` varchar(100) NOT NULL,
          `logourl` varchar(255) NOT NULL,
          `linkurl` varchar(255) NOT NULL,
          PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8");
        $system=D('System')->where("name='flinkpos'")->find();
        if (!$system) {
            $model->query("INSERT INTO `".C('DB_PREFIX')."system` (`name` ,`title` ,`contents` ,`description`)VALUES ('flinkpos', '友情链接显示位置', '10', '友情链接显示位置')");
        }
        $this->cacheupdate();
        return true;
    }

    //插件卸载
    public function uninstall() {
        $model=new model();
        $model->query("DROP TABLE IF EXISTS `".C('DB_PREFIX')."friendlink`");
        $model->query("DELETE FROM `".C('DB_PREFIX')."system` WHERE `name`='flinkpos'");
        $this->cacheupdate();
        return true;
    }

    private function cacheupdate() {
        $path='./Home/Runtime/Data';
        $this->deleteDir($path);
        mkdir($path);
        chmod($path,0777);

        $site=array();
        $data = M('system')->select();
        foreach ($data as $key=>$val) {
            $site[$val['name']]=$val['contents'];
        }
        F('site',$site,'./Home/Runtime/Data/');
    }

    private function deleteDir($dirName){
        if(!is_dir($dirName)){
            @unlink($dirName);
            return false;
        }
        $handle = @opendir($dirName);
        while(($file = @readdir($handle)) !== false){
            if($file != '.' && $file != '..'){
                $dir = $dirName . '/' . $file;
                is_dir($dir) ? $this->deleteDir($dir) : @unlink($dir);
            }
        }
        closedir($handle);
        return rmdir($dirName);
    }
}
?>