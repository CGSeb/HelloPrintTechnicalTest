<?php
include './Entity/User.php';
include './Tools/DbConnector.php';
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-type: application/json');
    header("Access-Control-Allow-Origin: *");

    $request = json_decode(file_get_contents('php://input'), true);
    $username = $request['username'];

    if (!$username) {
        echo json_encode([
            'success' => false,
            'message' => "Username must be set!",
        ]);
        exit();
    }

    $dbConnector = new DbConnector('helloprint-db', 'root', 'root', 3306);

    $user = $dbConnector->getUserByUsername($username);

    if (!($user instanceof User)) {
        echo json_encode([
            'success' => false,
            'message' => "This username doesn't exist!",
        ]);
        exit();
    }

    $message = [
        "Type" => "PasswordRecovery",
        "username" => $username,
    ];
    $rabbitMessage = json_encode($message);

    $connection = new AMQPStreamConnection('helloprint-core', 15672, 'rabbitmq', 'rabbitmq');
    $channel = $connection->channel();
    $channel->queue_declare('task_queue', false, true, false, false);
    $data = implode(' ', array_slice($argv, 1));
    if (empty($data)) {
        $data = "Hello World!";
    }
    $msg = new AMQPMessage(
        $data,
        array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
    );
    $channel->basic_publish($msg, '', 'task_queue');
    echo ' [x] Sent ', $data, "\n";
    $channel->close();
    $connection->close();
}else{ ?>
    <h1>Wrong method!</h1>
<?php } ?>