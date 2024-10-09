<?php
session_start();

use App\Database;
use App\Service;

require '../init.php';

// Kontrola, zda je uživatel přihlášen
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
$userId = $user['id'];

// Připojeni k databázi a Service instance
$database = new Database();
$service = new Service($database);

// Proměnné pro chybové a úspěšné zprávy
$errorMessage = "";
$success = ''; 

// Nastavení typu odpovědi na základě Accept hlavičky
$responseType = $_SERVER['HTTP_ACCEPT'] ?? 'text/html';

// Kontrola typu požadavku
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
        $input = json_decode(file_get_contents('php://input'), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $errorMessage = "Invalid JSON input: " . json_last_error_msg();
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $errorMessage]);
            exit();
        }

        $serviceName = $input['service_name'] ?? '';
        $serviceUserName = $input['service_user_name'] ?? '';
        $servicePassword = $input['service_user_password'] ?? '';
    } else {
        $serviceName = $_POST['service_name'] ?? '';
        $serviceUserName = $_POST['service_user_name'] ?? '';
        $servicePassword = $_POST['service_user_password'] ?? '';
    }

    // Validace vstupních dat
    if (empty($serviceName) || empty($serviceUserName) || empty($servicePassword)) {
        $errorMessage = "All fields are mandatory!";
    } else {
        try {
            // Přidání služby
            if ($service->addService($userId, $serviceName, $serviceUserName, $servicePassword)) {
                $success = "Password successfully added!";
            } else {
                $errorMessage = "Failed to add the password.";
            }
        } catch (Exception $e) {
            $errorMessage = "Error: " . $e->getMessage();
        }
    }

    if ($responseType == 'application/json') {
        header("Content-Type: application/json");
        echo json_encode([
            'status' => $errorMessage ? 'error' : 'success',
            'message' => $errorMessage ?: $success
        ]);
        exit();
    }
}

header("Content-Type: text/html");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../static/css/dashboard.css">
</head>
<body>
    <section class="dashboard">
        <h2>Add password section</h2>
        <?php require("menu.php");?>
        <form action="add_service.php" method="POST">
            <input type="hidden" name="action" value="add">
            <input type="text" name="service_name" placeholder="Service name">
            <input type="text" name="service_user_name" placeholder="Service user name">
            <input type="password" name="service_user_password" placeholder="Service user password">
            <input type="submit" value="Add password"><input type="reset" value="Reset form">
        </form>
        <?php if ($errorMessage): ?>
            <div class="error"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
    </section>
</body>
</html>
