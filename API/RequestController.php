<?php
namespace API;

use API\Entity\User;
use API\Tools\DbConnector;
use API\Tools\Producer;
use PhpAmqpLib\Message\AMQPMessage;

class RequestController {

    private $username;

    public function __construct($request)
    {
        $this->username = $request['username'];
        $this->assertUserExist();
    }

    private function assertUserExist()
    {
        if (!$this->username) {
            echo json_encode([
                'success' => false,
                'message' => "Username must be set!",
            ]);
            exit();
        }

        $dbConnector = new DbConnector('helloprint-db', 'root', 'root', 3306);

        $user = $dbConnector->getUserByUsername($this->username);

        if (!($user instanceof User)) {
            echo json_encode([
                'success' => false,
                'message' => "This username doesn't exist!",
            ]);
            exit();
        }
    }

    public function sendEmail(){
        $message = [
            "Type" => "PasswordRecovery",
            "username" => $this->username,
        ];
        $rabbitMessage = json_encode($message);
        $producer = new Producer();

        $msg = new AMQPMessage(
            $rabbitMessage,
            array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
        );
        $producer->publish($msg);
        $producer->close();
    }
}