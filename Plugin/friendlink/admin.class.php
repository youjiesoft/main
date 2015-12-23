<?php
if (!defined('IN_ET')) exit();

class friendlink_admin {

    public function admin() {
		$_site=include(ET_ROOT.'/Home/Runtime/Data/site.php');
        $pos=json_decode($_site['flinkpos'],true);

        if (in_array('pub',$pos)) {
            $pubck='checked';
        }
        if (in_array('home',$pos)) {
            $homeck='checked';
        }
        if (in_array('profile',$pos)) {
            $prock='checked';
        }

        $res.="<form action='".SITE_URL."/admin.php?s=/Plugins/doadmin/appname/friendlink/action/dosaveset' method='POST'>
        <table style='margin-top:5px' class='table'>
		<tr>
            <td width='50px' valign='top'>选择要显示的位置:</td>
            <td width='330px' style='text-indent:0px'>
            <input type='checkbox' name='showpos[]' value='pub' id='p_pub' ".$pubck."><label for='p_pub'> 广场侧栏底部</label><br/><br/>
            <input type='checkbox' name='showpos[]' value='home' id='p_home' ".$homeck."><label for='p_home'> 主页侧栏底部</label><br/><br/>
            <input type='checkbox' name='showpos[]' value='profile' id='p_pro' ".$prock."><label for='p_pro'> 空间侧栏底部</label>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td style='text-indent:0px'><input type='submit' class='button1' value='保存提交'></td>
        </tr>
        </table>
        </form>";
        $res.="<h3>友情链接 管理</h3>";
        $res.="<form action='".SITE_URL."/admin.php?s=/Plugins/doadmin/appname/friendlink/action/dosavelink' method='POST'>
        <table class='table' style='margin:5px 0 20px 0'>
        <tr>
            <td width='70px'>&nbsp;</td>
            <td width='150px'><b>链接名称</b></td>
            <td width='150px'><b>链接地址</b></td>
            <td><b>链接LOGO地址</b>(可以为空)</td>
        </tr>";
        $linkdata=D('Friendlink')->select();
        if ($linkdata) {
            foreach($linkdata as $val){
                $res.="<tr>
                    <td><input type='checkbox' name='delid[]' value='".$val['id']."' class='checkbox'></td>
                    <td><input type='text' name='name[".$val['id']."]' value='".$val['name']."' class='txt_input' style='width:130px'></td>
                    <td><input type='text' name='link[".$val['id']."]' value='".$val['linkurl']."' class='txt_input' style='width:130px'></td>
                    <td><input type='text' name='logo[".$val['id']."]' value='".$val['logourl']."' class='txt_input' style='width:130px'></td>
                </tr>";
            }
        }
        $res.="<tr id='addbtn'>
            <td>&nbsp;</td>
            <td colspan='3'><a href='javascript:void(0)' onclick='addips()'>+ 添加链接</a></td>
        </tr>
        <tr>
            <td><input type='checkbox' onclick='CheckAll(\"delid\",\"chkall\")' id='chkall' name='chkall' class='checkbox'> 删除?</td>
            <td colspan='3'><input type='submit' class='button1' value='提交保存'></td>
        </tr>
        </table>
        </form>
        <script type='text/javascript'>
        function addips() {
            $('#addbtn').before('<tr><td>&nbsp;</td><td><input type=\"text\" name=\"n_name[]\" value=\"\" class=\"txt_input\" style=\"width:130px\"></td><td><input type=\"text\" name=\"n_link[]\" class=\"txt_input\" style=\"width:130px\" value=\"http://\"></td><td><input type=\"text\" name=\"n_logo[]\" class=\"txt_input\" style=\"width:130px\" value=\"\"></td></tr>');
        }
        </script>";

        return $res;
    }

    public function dosavelink() {
        $fModel=D('Friendlink');
        $delid=$_POST['delid'];
        $name=$_POST['name'];
        $link=$_POST['link'];
        $logo=$_POST['logo'];
        $n_name=$_POST['n_name'];
        $n_link=$_POST['n_link'];
        $n_logo=$_POST['n_logo'];

        $linkdata = $fModel->select();
        //修改
        if ($linkdata) {
            foreach ($linkdata as $val) {
                if (in_array($val['id'],$delid)) {
                    $fModel->where("id='$val[id]'")->delete();
                } else {
                    if ($val['name']!=$name[$val['id']] || $val['linkurl']!=$link[$val['id']] || $val['logourl']!=$logo[$val['id']]) {
                        $fModel->where("id='$val[id]'")->setField(array('name','linkurl','logourl'),array($name[$val['id']],$link[$val['id']],$logo[$val['id']]));
                    }
                }
            }
        }
        //新增
        if ($n_name) {
            foreach ($n_name as $key=>$val) {
                if ($val && $n_link[$key]) {
                    $insert['name']=$val;
                    $insert['linkurl']=$n_link[$key];
                    $insert['logourl']=$n_logo[$key];
                    $fModel->add($insert);
                }
            }
        }
        //缓存
        $linkdata = $fModel->select();
        $this->deleteDir('./Home/Runtime/Data/friendlink.php');
        F('friendlink',$linkdata,'./Home/Runtime/Data/');
        msgreturn('友情链接插件保存成功！',SITE_URL.'/admin.php?s=/Plugins/appsetting/appname/friendlink');
    }

    public function dosaveset() {
        $showpos=json_encode($_POST['showpos']);

        D('System')->where("name='flinkpos'")->setField('contents',$showpos);

        $this->cacheupdate();

        msgreturn('友情链接插件保存成功！',SITE_URL.'/admin.php?s=/Plugins/appsetting/appname/friendlink');
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