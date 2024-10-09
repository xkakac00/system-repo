<?php
session_start();

require '../init.php';

use App\Database;
use App\User;

$database = new Database();
$user = new User($database);

$error = ''; // Inicializace proměnné pro chybu
$successMessage = ''; // Inicializace proměnné pro úspěch

$responseType = $_SERVER['HTTP_ACCEPT'] ?? 'text/html';

// Nastavte výchozí typ odpovědi na HTML
$contentType = 'text/html';

if ($responseType === "application/json") {
    $contentType = 'application/json';
    $input = json_decode(file_get_contents("php://input"), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        $error = "Invalid JSON input: " . json_last_error_msg();
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => $error]);
        exit();
    }

    $userName = $input['user_name'] ?? '';
    $password = $input['password'] ?? '';
} else {
    $userName = $_POST['user_name'] ?? '';
    $password = $_POST['password'] ?? '';
}

try {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if ($user->login($userName, $password)) {
            if ($responseType === 'application/json') {
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Login successful'
                ]);
                exit();
            } else {
                header("Location: dashboard.php");
                exit();
            }
        } else {
            $error = "Login failed!";
        }
    }
} catch (Exception $e) {
    $error = "Error: " . $e->getMessage();
}

if ($responseType === 'application/json' && !empty($error)) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => $error
    ]);
    exit();
}

header('Content-Type: text/html');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="../static/css/login.css">
</head>
<body>
    <div class="login-container">
        <h1>Login Page</h1>
        <form action="login.php" method="POST">
            <table>
                <tr>
                    <td>User name:</td>
                    <td><input type="text" name="user_name"></td>
                </tr>
                <tr>
                    <td>Password:</td>
                    <td><input type="password" name="password"></td>
                </tr>
                <tr>
                    <td><input type="submit" value="Login user"></td>
                    <td><input type="reset" value="Reset form"></td>
                </tr>
            </table>
        </form>
        <a href="register.php">Register</a>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
