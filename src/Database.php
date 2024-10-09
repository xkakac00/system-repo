<?php
namespace App;
use PDO;
use PDOException;

class Database {
    /* Definice vlasností */
    private $host = "localhost";
    private $dbname = "password_manager";
    private $username = "root";
    private $password = "";
    private $conn;

    /* Definice konstruktoru */
    public function __construct() {
        $this->connect();
    }

    /* Definice metody connect - kde přistupuji k databázi */
    private function connect() {
        try {
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->dbname}", $this->username, $this->password);  /* pdo - rozhraní pro přístup k databázím, řetězec pro informace pro připojení k databázi, uživatelské jméno a heslo */
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); /* ATTR_ERRMODE - způsob jak PDO hlásí chyby, ERRMODE_EXCEPTION - mód, kterým PDO vyvolá výjimku, vždy když dojde k chybě. */
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }

    /* metoda, která poskytuje přístup k privátní vlastnosti $conn */
    public function getConnection() {
        return $this->conn;
    }

    /* Odpojení od databáze */
    public function disconnect() {
        $this->conn = null;
    }
}
?>
