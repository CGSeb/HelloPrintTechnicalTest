<?php
namespace API\Tools;

use PhpAmqpLib\Message\AMQPMessage;

class LoginProducer extends AbstractQueue {

    private $callbackQueue;

    /**
     * @var string
     */
    private $corrId;

    /**
     * @var boolean
     */
    private $hasResponded;

    public function __construct()
    {
        parent::__construct();

        $this->channel = $this->connection->channel();
        list($this->callbackQueue, ,) = $this->channel->queue_declare(
            "",
            false,
            false,
            true,
            false
        );

        $this->channel->basic_consume(
            $this->callbackQueue,
            '',
            false,
            true,
            false,
            false,
            array(
                $this,
                'onResponse'
            )
        );
    }

    public function onResponse($rep)
    {
        if ($rep->get('correlation_id') == $this->corrId) {
            $response = json_decode($rep->body, true);
            $this->hasResponded = true;
            $this->closeQueue();
            $this->getLoginResponse($response);
        }
    }


    public function publish($username, $password, $loginUuid)
    {
        $this->corrId = $loginUuid;
        $this->hasResponded = false;
        $msg = $this->createRabbitMessage($username, $password);

        $this->channel->basic_publish($msg, '', self::RABBITMQ_LOGIN_QUEUE);

        while (!$this->hasResponded) {
            $this->channel->wait();
        }
    }

    /**
     * @param $loginUuid
     * @return AMQPMessage
     */
    private function createRabbitMessage($username, $password)
    {
        $message = [
            "username" => $username,
            "password" => $password,
        ];
        $rabbitMessage = json_encode($message);
        $msg = new AMQPMessage(
            $rabbitMessage,
            [
                'correlation_id' => $this->corrId,
                'reply_to' => $this->callbackQueue
            ]
        );

        return $msg;
    }

    /**
     * @param array $response
     */
    private function getLoginResponse($response)
    {
        $userName = $response['username'];
        $email = $response['email'];
        $error = $response['error'] ?? '';

        if ($error !== '') {
            echo json_encode([
                'success' => false,
                'message' => $error,
            ]);
            exit();
        }

        echo json_encode([
            'success' => true,
            'username' => $userName,
            'email' => $email,
        ]);
        exit();

    }

}
