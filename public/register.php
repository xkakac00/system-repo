<?php
session_start();
require '../init.php';

use App\Database;
use App\User;

// Inicializace tříd
$database = new Database();
$user = new User($database);

$error = ''; // Proměnná pro chybové zprávy
$success = ''; // Proměnná pro úspěšné zprávy

// Detekce typu požadavku
$responseType = $_SERVER['HTTP_ACCEPT'] ?? 'text/html';

// Kontrola, zda je požadavek JSON
if ($responseType == "application/json") {
    // Čtení dat z JSON vstupu
    $input = json_decode(file_get_contents("php://input"), true);
    $fullName = $input['full_name'] ?? '';
    $userName = $input['user_name'] ?? '';
    $password = $input['password'] ?? '';
} else {
    // Čtení dat z POST požadavku
    $fullName = $_POST['full_name'] ?? '';
    $userName = $_POST['user_name'] ?? '';
    $password = $_POST['password'] ?? '';
}

try {
    // Zpracování POST požadavku
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validace vstupních dat
        if (empty($fullName) || empty($userName) || empty($password)) {
            $error = "All fields are required!";
        } else {
            // Kontrola, zda uživatelské jméno již existuje
            if ($user->userExists($userName)) {
                $error = "The user with this name already exists.";
            } else {
                // Pokus o registraci uživatele
                if ($user->register($fullName, $userName, $password)) {
                    $success = "The User has been successfully registered!"; // Nastavení úspěšné hlášky
                    
                    // Pokud je požadavek JSON, vrátíme JSON odpověď
                    if ($responseType == 'application/json') {
                        header("Content-Type: application/json");
                        echo json_encode([
                            'status' => 'success',
                            'message' => $success
                        ]);
                        exit();
                    }
                } else {
                    $error = "Registration failed. Please try again.";
                }
            }
        }
    }
} catch (Exception $e) {
    $error = "Error: " . $e->getMessage();
}

// Zpracování chyby pro JSON odpověď
if ($responseType == 'application/json' && !empty($error)) {
    header("Content-Type: application/json");
    echo json_encode([
        'status' => 'error',
        'message' => $error
    ]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>
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

        .login-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
        }

        td {
            padding: 10px 0;
        }

        input[type="text"],
        input[type="password"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"],
        input[type="reset"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: none;
            border-radius: 4px;
            background-color: #007BFF;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        input[type="reset"] {
            background-color: #6c757d;
        }

        input[type="submit"]:hover,
        input[type="reset"]:hover {
            background-color: #0056b3;
        }

        input[type="reset"]:hover {
            background-color: #5a6268;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #007BFF;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .error, .success {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-top: 20px;
            text-align: center;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Register Page</h1>
        <form action="register.php" method="POST">
            <table>
                <tr>
                    <td>Full name:</td>
                    <td><input type="text" name="full_name"></td>
                </tr>
                <tr>
                    <td>User name:</td>
                    <td><input type="text" name="user_name"></td>
                </tr>
                <tr>
                    <td>Password:</td>
                    <td><input type="password" name="password"></td>
                </tr>
                <tr>
                    <td><input type="submit" value="Register"></td>
                    <td><input type="reset" value="Reset form"></td>
                </tr>
            </table>
        </form>
        <a href="login.php">Login</a>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
