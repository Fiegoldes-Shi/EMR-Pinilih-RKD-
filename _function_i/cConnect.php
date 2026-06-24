<?php
class cConnect
{
    private $dbHost;
    private $dbUser;
    private $dbPass;
    private $dbName;

     public function __construct() {
         $this->dbHost = getenv('DB_HOST') ?: '127.0.0.1';
         $this->dbUser = getenv('DB_USER') ?: 'root';
         $this->dbPass = getenv('DB_PASS') ?: '';
         $this->dbName = getenv('DB_NAME') ?: 'emr_pinilih';
     }

    function goConnect()
    {
        $conn = mysqli_connect($this->dbHost, $this->dbUser, $this->dbPass, $this->dbName);

        if (mysqli_connect_error()) {
            die("Database connection error: " . mysqli_connect_error());
        }

        $GLOBALS["conn"] = $conn;
    }
}