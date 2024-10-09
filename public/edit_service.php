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

// Připojení k databázi a vytvoření instance třídy Service
$database = new Database();
$service = new Service($database);

$error = ''; // Inicializace proměnné pro chybovou zprávu
$successMessage = ''; // Inicializace proměnné pro úspěšnou zprávu

$responseType = $_SERVER['HTTP_ACCEPT'] ?? 'text/html';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($responseType == 'application/json') {
        // Zpracování JSON požadavku
        $input = json_decode(file_get_contents("php://input"), true);
        $serviceId = $input['id'] ?? ''; // Použití aktualizovaných názvů
        $serviceName = $input['updated_service_name'] ?? ''; 
        $serviceUserName = $input['updated_service_user_name'] ?? ''; 
        $servicePassword = $input['updated_service_user_password'] ?? ''; 
    } else {
        // Zpracování HTML formuláře
        $serviceId = $_POST['id'] ?? '';
        $serviceName = $_POST['updated_service_name'] ?? ''; 
        $serviceUserName = $_POST['updated_service_user_name'] ?? ''; 
        $servicePassword = $_POST['updated_service_user_password'] ?? ''; 
    }

    // Validace vstupních dat
    if (empty($serviceName) || empty($serviceUserName) || empty($servicePassword)) {
        $error = "All rows are mandatory!";
    } else {
        if ($service->editService($serviceId, $userId, $serviceName, $serviceUserName, $servicePassword)) {
            $successMessage = "Password successfully updated";

            // Odpověď pro JSON požadavek
            if ($responseType == 'application/json') {
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'success',
                    'message' => $successMessage
                ]);
                exit();
            }

            // Přesměrování pro HTML formulář
            sleep(1);
            header("Location: view_services.php");
            exit();
        } else {
            $error = "An error occurred while updating the service.";
        }
    }

    // Odpověď pro JSON požadavek v případě chyby
    if ($responseType == 'application/json' && !empty($error)) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => $error
        ]);
        exit();
    }
} else {
    if (isset($_GET['id'])) {
        $serviceId = $_GET['id'];
        $serviceDetails = $service->getServiceById($serviceId, $userId);
        if (!$serviceDetails) {
            $error = "Passwords not found.";
        }
    } else {
        header("Location: view_services.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editing password</title>
    <link rel="stylesheet" href="../static/css/dashboard.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .dashboard {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 800px;
        }

        .dashboard-body h2 {
            margin-top: 0;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        form input[type="text"], form input[type="password"] {
            padding: 10px;
            margin: 5px 0 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        form input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        form input[type="submit"]:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
            background-color: #f8d7da;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <section class="dashboard">
        <section class="dashboard-body">
            <h2>Editing password</h2>
            <?php require("menu.php"); ?>
            <?php if (!empty($error)): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if (isset($serviceDetails)): ?>
                <form action="edit_service.php?id=<?php echo htmlspecialchars($serviceDetails['id']); ?>" method="POST">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($serviceDetails['id']); ?>">
                    <input type="text" name="updated_service_name" placeholder="Service name" value="<?php echo htmlspecialchars($serviceDetails['service_name']); ?>">
                    <input type="text" name="updated_service_user_name" placeholder="Service user name" value="<?php echo htmlspecialchars($serviceDetails['user_name']); ?>">
                    <input type="password" name="updated_service_user_password" placeholder="Service user password" value="<?php echo htmlspecialchars($serviceDetails['user_password']); ?>">
                    <input type="submit" value="Update the password">
                </form>
            <?php endif; ?>
            <nav>
                <ul>
                    <li><a href="view_services.php">Back to show all passwords</a></li>
                </ul>
            </nav>
        </section>
    </section>
</body>
</html>
