<?php

class Database {
    protected static $_dbInstance = null;
    protected $_dbHandle;

    public static function getInstance() {
       $username = 'u202103011';
       $password = 'u202103011';
       $host = '20.126.5.244';
       $dbName = 'db202103011';

       if (self::$_dbInstance === null) {
            self::$_dbInstance = new self($username, $password, $host, $dbName);
       }

       return self::$_dbInstance;
    }

    private function __construct($username, $password, $host, $database) {
        try {
            $this->_dbHandle = new PDO("mysql:host=$host;dbname=$database",  $username, $password);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getdbConnection() {
        return $this->_dbHandle;
    }

    public function __destruct() {
        $this->_dbHandle = null;
    }
}
