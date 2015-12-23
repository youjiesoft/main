<?php
class CommonAdvModel extends AdvModel {     //重点是继承于AdvModel类而非Model类
    /**
     * 通过建立新的数据库连接，实现a库与b库之间切换
     * @param string $dbobj  数据库对象  默认为a
     * @return void
     */
    public function connectDataBase($dbobj='b',$table=''){
        $db_connect = array(
            'dbms'     => 'mysql',
            'username' => C('DB_USER'),
            'password' => C('DB_PWD'),
            'hostname' => C('DB_HOST'),
            'hostport' => '3306'
        );

        if($dbobj=='a'){
            $db_connect['database'] =C('DB_NAME');
        }else{
            $db_connect['database'] ='information_schema';
        }

        //将这个配置添加到第1个上
        if($this->addConnect($db_connect,1)===false)
            die('无法新增数据库配置！');
        if($this->switchConnect(1)===false)    //并进行配置转换
            die('无法切换数据库！');
    }

   //其他常用代码
}
?>