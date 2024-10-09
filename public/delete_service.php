<?php
session_start();

use App\Database;
use App\Service;

require '../init.php';

$responseType = $_SERVER['HTTP_ACCEPT'] ?? 'text/html';

// Kontrola, zda je uživatel přihlášen
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
$userId = $user['id'];

// Inicializace proměnných
$error = null;
$message = null;

// Připojení k databázi a vytvoření instance třídy Service
$database = new Database();
$service = new Service($database);

// Zkontrolujeme, zda byl předán parametr 'id' přes GET
if (isset($_GET['id'])) {
    $serviceId = $_GET['id'];

    // Kontrola, zda služba existuje před smazáním
    $existingService = $service->getServiceById($serviceId, $userId);

    if ($existingService) {
        if ($service->deleteService($serviceId, $userId)) {
            $message = "The service has been successfully removed.";
        } else {
            $error = "A service removal error occurred.";
        }
    } else {
        $error = "Service does not exist.";
    }
}

// Načtení všech služeb pro přihlášeného uživatele
$services = $service->getAllServices($userId);

// Pokud je požadavek JSON, vrátím JSON odpověď
if ($responseType == 'application/json') {
    header("Content-Type: application/json");
    echo json_encode([
        'status' => $error ? 'error' : 'success',
        'message' => $error ?: $message,
        'services' => $services
    ]);
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Odstranit službu</title>
    <link rel="stylesheet" href="../static/css/dashboard.css">
</head>
<body>
    <section class="dashboard">
        <section class="dashboard-body">
            <h2>Remove password.</h2>
            <?php require("menu.php"); ?>
            <?php if ($error): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php elseif ($message): ?>
                <p class="success"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Service name</th>
                            <th>User name</th>
                            <th>Password</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($services)): ?>
                            <?php foreach ($services as $service): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($service['service_name']); ?></td>
                                    <td><?php echo htmlspecialchars($service['user_name']); ?></td>
                                    <td><?php echo htmlspecialchars($service['user_password']); ?></td>
                                    <td>
                                        <a href="delete_service.php?id=<?php echo $service['id']; ?>" name="remove">Remove password</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">No services were found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </section>
</body>
</html>
