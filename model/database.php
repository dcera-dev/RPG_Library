<?php
    namespace Models;
    class Database {
        private $host = 'localhost';
        private $dbName = 'rpg_library';
        private $user = 'db_admin';
        private $pass = '6TusdNdKdUoACkHm';

        //Connection
        private $conn;
        private $conn_err = '';

        //Construct
        function __construct() {
            mysqli_report(MYSQLI_REPORT_OFF);
            $this->conn = mysqli_connect($this->host, $this->user, $this->pass, $this->dbName);

            if ($this->conn === false) {
                $this->conn_err = 'Failed to connect to database: ' . mysqli_connect_error();
            }
        }

        //Destruct
        function __destruct() {
            mysqli_close($this->conn);
        }

        //Return Connection
        function getConnection() {
            //getConnection returns either the connection error, or the connection object.
            if ($this->conn === false) return $this->conn_err;
            else return $this->conn;
        }

        //Return Database Information Object
        function getDatabaseInformation() {
            return new DatabaseInformation($this->host, $this->dbName, $this->user, $this->pass);
        }
    }
    //Helper class for returning database information in one object.
    class DatabaseInformation {
        //Params
        public $host;
        public $dbName;
        public $user;
        public $pass;

        //Contstruct
        function __construct($host, $name, $user, $pass) {
            $this->host = $host;
            $this->dbName = $name;
            $this->user = $user;
            $this->pass = $pass;
        }
    }