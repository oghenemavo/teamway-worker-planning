<?php

namespace App\Handler;

use PDO;
use PDOException;
use Exception;

class DatabaseHandler {

    private $_connection;
    private $_db;
    private $_host;
    private $_dbName;
    private $_user;
    private $_pass;
    private static $_instance;

    private function __clone() {}

    private function __construct() {
    }

    public function connect(string $db, string $host, string $dbName, string $user, string $pass): object|null {
        $this->_db = $db;
        $this->_host = $host . ';';
        $this->_dbName = $dbName;
        $this->_user = $user;
        $this->_pass = $pass;

        $this->_connection = new PDO($this->_db. ':host=' . $this->_host . 'dbname=' . $this->_dbName, $this->_user, $this->_pass);
        $this->_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);

        return $this->_connection;
    }

    public static function getInstance() {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
}