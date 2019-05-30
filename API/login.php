<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/classLoader.php';
use API\LoginController;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-type: application/json');
    header("Access-Control-Allow-Origin: *");

    $request = json_decode(file_get_contents('php://input'), true);
    $loginController = new LoginController();
    $loginController->sendLoginRequest($request);
}else{ ?>
    <h1>Wrong method!</h1>
<?php } ?>