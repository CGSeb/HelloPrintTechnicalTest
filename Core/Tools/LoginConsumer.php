<?php
namespace Core\Tools;

use Core\Entity\User;
use PhpAmqpLib\Message\AMQPMessage;

class LoginConsumer extends AbstractQueue {

    public function __construct()
    {
        parent::__construct();
        $this->initChannel();
    }

    private function initChannel()
    {
        $this->channel = $this->connection->channel();
        $this->channel->queue_declare(self::RABBITMQ_LOGIN_QUEUE, false, false, false, false);
    }

    public function consume()
    {
        echo ' [*] Waiting for login request. To exit press CTRL+C', "\n";

        $callback = function($req){
            $body = $req->body;
            echo " [x] Received ", $body, "\n";
            $request = json_decode($body, true);
            $username = $request['username'];
            $password = $request['password'];


            $msg = new AMQPMessage(
                (string) $this->authenticateUser($username, $password),
                [
                    'correlation_id' => $req->get('correlation_id'),
                ]
            );

            $req->delivery_info['channel']->basic_publish(
                $msg,
                '',
                $req->get('reply_to')
            );
            $req->delivery_info['channel']->basic_ack(
                $req->delivery_info['delivery_tag']
            );
        };

        $this->channel->basic_qos(null, 1, null);
        $this->channel->basic_consume(self::RABBITMQ_LOGIN_QUEUE, '', false, false, false, false, $callback);

        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }

        $this->closeQueue();
    }

    public function authenticateUser($username, $password): string
    {
        $dbConnector = new DbConnector('helloprint-db', 'root', 'root', 3306);
        $user = $dbConnector->getUserByUsername($username);
        $dbConnector->close();

        if (!($user instanceof User)) {
            return json_encode([
                'success' => false,
                'error' => "This user doesn't exist!",
            ]);
        }

        $userPassword = $user->getPassword();

        if ($userPassword !== $password) {
            return json_encode([
                'success' => false,
                'error' => "Wrong password!",
            ]);
        }

        return json_encode([
            'success' => true,
            'username' => $username,
            'email' => $user->getEmail(),
        ]);
    }
}
