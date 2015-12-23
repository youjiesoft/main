<?php

class InformationSchemaModel extends CommonAdvModel{
    //protected $tableName = 'TABLES';

    public function __construct() {
        parent::__construct();
        $this->connectDataBase('b');
    }
}

?>