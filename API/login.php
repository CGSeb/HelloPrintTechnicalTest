<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/classLoader.php';
use API\LoginController;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-type: application/json');
    header("Access-Control-Allow-Origin: *");

    $request = json_decode(file_get_contents('php://input'), true);

    try {
        $loginController = new LoginController();
        $loginController->sendLoginRequest($request);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error while trying to login!',
        ]);
        exit();
    }
}else{ ?>
    <h1>Wrong method!</h1>
<?php } ?>