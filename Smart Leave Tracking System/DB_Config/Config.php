<?php
class dbconfig
{
    private $servername = "localhost";
    private $username   = "root";
    private $password   = "";
    private $dbname     = "lms_db";
    private $conn       = null;

    public function getConnection()
    {
        $this->conn = mysqli_connect($this->servername, $this->username, $this->password, $this->dbname);

        if (!$this->conn) {
            die("Connection Failed: " . mysqli_connect_error());
        }

        return $this->conn;
    }
}
?>