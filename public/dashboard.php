<?php
session_start();

use App\Database;

require '../init.php';

// Kontrola, zda je uživatel přihlášen
if (!isset($_SESSION['user'])) {
    // Pokud uživatel není přihlášen, přesměrujeme ho na login.php
    header("Location: login.php");
    exit();
}
$user=$_SESSION['user'];
$userName=$user['user_name']
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
            <section class="dashboard-body">
                    <h2>Welcome to the dashboard:<?php echo htmlspecialchars($userName); ?>!</h2>
                    <p>Our password management app allows you to securely store and manage your login credentials for various services</p>
                    <?php require ("menu.php");?>

             </section>
        

       
    </section>
</body>
</html>
