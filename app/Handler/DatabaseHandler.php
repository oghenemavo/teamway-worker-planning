<?php

namespace App\Handler;

use PDO;
use PDOException;
use Exception;

class DatabaseHandler {

    private $_connection;

    // private function __clone() {}

    public function __construct(private string $_db, private string $_host, 
        private string $_dbName, private string $_user, private string $_pass
    ) {
    }

    public function connect(): PDO {
        $this->_connection = new PDO($this->_db. ':host=' . $this->_host . ';dbname=' . $this->_dbName, $this->_user, $this->_pass);
        $this->_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);

        return $this->_connection;
    }

    public function __destruct()
    {
        $this->_connection = null;
    }
    
}