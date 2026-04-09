<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

class Database {
    private $host = '127.0.0.1';
    private $db_name = 'user_auth_db';
    private $username = 'root';
    private $password = '';

    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->db_name}", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            die("Ошибка соединения: " . $exception->getMessage());
        }
        return $this->conn;
    }
}
?>