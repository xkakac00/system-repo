<?php
session_start();

use App\Database;
use App\Service;

require '../init.php';

// Kontrola, zda je uživatel přihlášen
if (!isset($_SESSION['user'])) {
    if ($_SERVER['HTTP_ACCEPT'] === 'application/json') {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
        exit();
    } else {
        // Pokud uživatel není přihlášen, přesměrujeme ho na login.php
        header("Location: login.php");
        exit();
    }
}

$user = $_SESSION['user'];
$userId = $user['id'];

// Připojeni k databázi a vytvoření instance třídy Service
$database = new Database();
$service = new Service($database);

// Načtení všech služeb pro přihlášeného uživatele
$services = $service->getAllServices($userId);

// Nastavení typu odpovědi na základě Accept hlavičky
$responseType = $_SERVER['HTTP_ACCEPT'] ?? 'text/html';

if ($responseType === 'application/json') {
    header("Content-Type: application/json");
    echo json_encode([
        'status' => 'success',
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
    <title>Dashboard</title>
    <link rel="stylesheet" href="../static/css/dashboard.css">
</head>
<body>
<section class="dashboard">
    <h2>Your passwords</h2>
    <?php require("menu.php"); ?>
    <table>
        <thead>
            <tr>
                <th>Service name</th>
                <th>User name</th>
                <th>Password</th>
                <th>Edit</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($services)): ?>
                <?php foreach ($services as $service): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($service['service_name']); ?></td>
                        <td><?php echo htmlspecialchars($service['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($service['user_password']); ?></td>
                        <td><a href="edit_service.php?id=<?php echo $service['id']; ?>" name="edit">Edit</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No services were found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>
</body>
</html>
