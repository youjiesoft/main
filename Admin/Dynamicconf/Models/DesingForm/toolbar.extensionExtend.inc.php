<?php 
/**
 * @Title: ExtendConfig
 * @Package package_name
 * @Description: todo(动态表单_配置文件-操作权限配置文件--扩展部分)
 * @author 管理员
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015-03-20 21:45:42
 * @version V1.0
*/
return array(
    'js-desing' => array(
        'ifcheck' => '1',
        'rules' => '#operateid#==0',
        'permisname' => 'desing_desing',
        'html' => '<a class="js-desing desing tml-btn tml_look_btn tml_mp"  href="__URL__/desing/id/{sid_node}" rel="__MODULE__edit" target="navTab" title="页面设计_布局"><span><span class="icon icon-edit icon_lrp"></span>布局</span></a>',
        'shows' => '1',
        'sortnum' => '5',
    ),
);

?>