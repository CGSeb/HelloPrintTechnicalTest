<?php
namespace API\Tools;

use PhpAmqpLib\Message\AMQPMessage;
use Ramsey\Uuid\Uuid;

class LoginProducer extends AbstractQueue {

    /**
     * @var string
     */
    private $callbackQueue;

    /**
     * @var string
     */
    private $corrId;

    /**
     * @var boolean
     */
    private $hasResponded;

    public function __construct($getResponse = [])
    {
        parent::__construct();
        $this->channel = $this->connection->channel();

        if (!isset($getResponse['request-id']) && !isset($getResponse['queue'])) {
            $this->callbackQueue = Uuid::uuid4();
            $this->channel->queue_declare(
                $this->callbackQueue,
                false,
                false,
                false,
                false,
                false
            );
        } else {
            $this->corrId = $getResponse['request-id'];
            $this->callbackQueue = $getResponse['queue'];
            $this->hasResponded = false;
            $this->channel->queue_declare($this->callbackQueue, false, false, false, false);
        }
    }

    public function publish($username, $password, $loginUuid): string
    {
        $this->corrId = $loginUuid;
        $this->hasResponded = false;
        $msg = $this->createRabbitMessage($username, $password);
        $this->channel->basic_publish($msg, '', self::RABBITMQ_LOGIN_QUEUE);

        return $this->callbackQueue;
    }

    public function consume()
    {
        $onResponse = function ($rep) {
            if ($rep->get('correlation_id') == $this->corrId) {
                $response = json_decode($rep->body, true);
                $this->hasResponded = true;
                $this->getLoginResponse($response);
            }
        };
        $this->channel->basic_qos(null, 1, null);
        $this->channel->basic_consume($this->callbackQueue, '', false, false, false, false, $onResponse);
        try {
            $this->channel->wait(null, false,1);
        } catch (\Exception $exception) {
            echo json_encode([
                'success' => true,
                'message' => 'pending',
            ]);
            exit();
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
        $this->channel->queue_delete($this->callbackQueue);
        $this->closeQueue();

        if ($error !== '') {
            echo json_encode([
                'success' => false,
                'message' => $error,
                'request-id' => $this->corrId,
                'queue' => $this->callbackQueue,
            ]);
            exit();
        }

        echo json_encode([
            'success' => true,
            'username' => $userName,
            'email' => $email,
            'request-id' => '',
            'queue' => '',
        ]);
        exit();

    }

}
