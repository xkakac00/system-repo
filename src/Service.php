<?php
namespace App;
use Exception;
use PDO;

class Service {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db->getConnection(); // předani přistupu k db
    }

    public function addService($userId, $serviceName, $serviceUserName, $servicePassword) {
        // validace vstupních dat
        if (empty($serviceName) || empty($serviceUserName) || empty($servicePassword)) {
            throw new Exception("All rows are mandatory!");
        }

        // Check if the service already exists for the user
        if ($this->serviceExists($userId, $serviceName, $serviceUserName)) {
            throw new Exception("Service already exists!");
        }

        $sql = "INSERT INTO passwords(user_id, service_name, user_name, user_password) VALUES (:user_id, :service_name, :user_name, :user_password)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':service_name', $serviceName);
        $stmt->bindParam(':user_name', $serviceUserName);
        // Hash hesla před jeho vložením do databáze
        //$hashedPassword = password_hash($servicePassword, PASSWORD_DEFAULT);
        $stmt->bindParam(':user_password', $servicePassword);
        return $stmt->execute();
    }

    private function serviceExists($userId, $serviceName, $serviceUserName) {
        $sql = "SELECT COUNT(*) FROM passwords WHERE user_id = :user_id AND service_name = :service_name AND user_name = :user_name";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':service_name', $serviceName);
        $stmt->bindParam(':user_name', $serviceUserName);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // Ostatní metody zůstávají beze změny

    public function editService($id, $userId, $serviceName, $serviceUserName, $servicePassword) {
        // validace vstupních dat
        if (empty($serviceName) || empty($serviceUserName) || empty($servicePassword)) {
            throw new Exception("All rows are mandatory!");
        }
        $sql = "UPDATE passwords SET service_name = :service_name, user_name = :user_name, user_password = :user_password WHERE id = :id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':service_name', $serviceName);
        $stmt->bindParam(':user_name', $serviceUserName);
        // Hash hesla před jeho aktualizací v databázi
        //$hashedPassword = password_hash($servicePassword, PASSWORD_DEFAULT);
        $stmt->bindParam(':user_password', $servicePassword);
        return $stmt->execute();
    }

    public function getAllServices($userId) {
        $sql = "SELECT id, service_name, user_name, user_password FROM passwords WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteService($id, $userId) {
        $sql = "DELETE FROM passwords WHERE id = :id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':user_id', $userId);
        return $stmt->execute();
    }

    public function getServiceById($id, $userId) {
        $sql = "SELECT id, service_name, user_name, user_password FROM passwords WHERE id = :id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
