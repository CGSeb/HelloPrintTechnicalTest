<?php
namespace Core\Tools;

use Core\Entity\User;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class Consumer {
    const RABBITMQ_HOST         = 'helloprint-core-rabbit';
    const RABBITMQ_PORT         = 5672;
    const RABBITMQ_USERNAME     = 'rabbitmq';
    const RABBITMQ_PASSWORD     = 'rabbitmq';
    const RABBITMQ_QUEUE_NAME   = 'helloprint-recovery';

    /**
     * @var AMQPStreamConnection
     */
    private $connection;

    /**
     * @var AMQPChannel
     */
    private $channel;

    /**
     * @var DbConnector
     */
    private $dbConnector;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            self::RABBITMQ_HOST,
            self::RABBITMQ_PORT,
            self::RABBITMQ_USERNAME,
            self::RABBITMQ_PASSWORD
        );

        $this->initChannel();
        $this->dbConnector = new DbConnector('helloprint-db', 'root', 'root', 3306);
    }

    private function initChannel()
    {
        $this->channel = $this->connection->channel();
        $this->channel->queue_declare(
            $queue = self::RABBITMQ_QUEUE_NAME,
            $passive = false,
            $durable = true,
            $exclusive = false,
            $auto_delete = false,
            $nowait = false,
            $arguments = null,
            $ticket = null
        );
    }

    public function consume()
    {
        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

        $callback = function($msg){
            $body = $msg->body;
            echo " [x] Received ", $body, "\n";

            $response = json_decode($body, true);
            $type = $response["Type"];

            if ($type == "PasswordRecovery") {
                $this->sendPasswordRecovery($response);
            }

            echo " [x] Done", "\n";
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };

        $this->channel->basic_qos(null, 1, null);

        $this->channel->basic_consume(
            $queue = self::RABBITMQ_QUEUE_NAME,
            $consumer_tag = '',
            $no_local = false,
            $no_ack = false,
            $exclusive = false,
            $nowait = false,
            $callback
        );

        while (count($this->channel->callbacks))
        {
            $this->channel->wait();
        }

        $this->close();
    }

    public function close()
    {
        $this->channel->close();
        $this->connection->close();
    }

    public function sendPasswordRecovery($response)
    {
        $userName = $response['username'];
        $user = $this->dbConnector->getUserByUsername($userName);

        if (!($user instanceof User)) {
            return;
        }

        $userEmail = $user->getEmail();
        $userPasword = $user->getPassword();

        $message = "<h1>Hi " . $userName . "</h1><h3>Here is your Password: " . $userPasword ."</h3>";

        $headers = [
            'From' => 'contact@helloprin.nl',
        ];

        $sent = mail($userEmail, 'Helloprint password recovery', $message, $headers);

        echo "User: " . $userName . " Email sent to: " . $userEmail . " Result: " . $sent . "\r\n";
    }
}
