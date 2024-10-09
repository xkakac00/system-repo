<?php
session_start();
require '../init.php';

use App\Database;
use App\User;

$database = new Database();
$user = new User($database);

$error = ''; // Inicializace proměnné pro chybu
$successMesasage=''; // Inicializace proměnné pro úspěch

$responseType = $_SERVER['HTTP_ACCEPT'] ?? 'text/html';

// Pokud je požadavek typu json
if ($responseType=="application/json"){
    $input=json_decode(file_get_contents("php://input"),true);
    $userName=$input['user_name'] ?? '';
    $password=$input['password'] ?? '';
}
// Pokud je to normální UI formulář
else{
    $userName=$_POST['user_name'] ?? '';
    $password=$_POST['password'] ?? '';
}

try{
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        // Přihlášení uživatele
        if ($user->login($userName,$password)){
            if ($responseType=='application/json'){
                // Pokud je požadavek json, vracim json odpověd
                echo json_encode([
                    'status'=>'success',
                    'message'=>'Login successful'
                ]);
                exit();

            } 
            // Přesměrování pro normální formulář.
            else{
                header("location:dashboard.php");
                exit();
            }
            }else{
                $error="Login failed!";
            }
        }
    }
catch(Exception $e){
    $error = "Error:".$e->getMessage();
}

if ($responseType == 'application/json' && !empty($error)) {
    echo json_encode([
        'status' => 'error',
        'message' => $error
    ]);
    exit();
}







#try {
#    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Načtení dat z POST požadavku
#        $userName = $_POST['user_name'] ?? '';  // ?? vrátí výchozí hodnotu '' pokud levá strana je null nebo není nastavena.
#        $password = $_POST['password'] ?? '';

#        // Pokus o přihlášení uživatele
#        if ($user->login($userName, $password)) {
#            header("Location: dashboard.php");
#            exit(); // zde už nepotřebuji, aby se další kod vykonal
#        } else {
#            $error = "Login failed!";
#        }
#    }
#} catch (Exception $e) {
    // getMessage() je metoda z třídy Exception
#    $error = "Error: " . $e->getMessage();
#}
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
