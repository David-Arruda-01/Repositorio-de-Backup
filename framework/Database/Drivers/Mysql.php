<?php

namespace Fmk\Database\Drivers;

class Mysql extends Driver{

    protected static $parameters_default = [
        'port'=>3306,
        'host'=>'localhost',
        'user'=> null,
        'password'=>null,
        'options'=>[\Pdo\Mysql::ATTR_INIT_COMMAND => 'SET NAMES utf8'],
    ];

    
    protected function getDSN()
    {
        return "mysql:host=".$this->host.
        ";port=".$this->port.";dbname=".$this->database;
    }

}
