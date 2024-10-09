<?php
namespace App;
use Exception;
use PDO;

class User {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db->getConnection();  // Zde dostanu přístup k databázi
    }

    public function register($fullName, $userName, $password) {
        // Validace vstupních dat
        if (empty($fullName) || empty($userName) || empty($password)) {
            throw new Exception("All rows are mandatory!");
        }
        // řešeno zde, protože při testování, xampp server vyhazoval chyby, že metoda bindParam si vyžadovala jako parametry proměnné.
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        // SQL dotaz
        $sql = "INSERT INTO users (full_name, user_name, user_password) VALUES (:full_name, :user_name, :user_password)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':full_name', $fullName);
        $stmt->bindParam(':user_name', $userName);
        $stmt->bindParam(':user_password',$hashedPassword);
        return $stmt->execute(); // Vrátí true nebo false
    }

    public function login($userName, $password) {
        $sql = "SELECT * FROM users WHERE user_name = :user_name";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_name', $userName);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['user_password'])) {
            $_SESSION['user']=$user;
            return $user;
        } else {
            return false;
        }
    }
    
    public function userExists($userName) {
        $sql = "SELECT COUNT(*) FROM users WHERE user_name = :user_name";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_name', $userName);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
    
}
?>