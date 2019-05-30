<?php
namespace API;

use API\Tools\RecoveryProducer;
use PhpAmqpLib\Message\AMQPMessage;

class RequestController {

    /**
     * @var string
     */
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
    }

    public function sendEmail(){
        $message = [
            "Type" => "PasswordRecovery",
            "username" => $this->username,
        ];
        $rabbitMessage = json_encode($message);
        $producer = new RecoveryProducer();

        $msg = new AMQPMessage(
            $rabbitMessage,
            array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
        );
        $producer->publish($msg);
        $producer->closeQueue();

        echo json_encode([
            'success' => true,
        ]);
        exit();
    }
}