<?php
namespace API\Tools;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Producer {
    const RABBITMQ_HOST         = 'helloprint-core';
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

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            self::RABBITMQ_HOST,
            self::RABBITMQ_PORT,
            self::RABBITMQ_USERNAME,
            self::RABBITMQ_PASSWORD
        );

        $this->initChannel();
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

    public function publish(AMQPMessage $message)
    {
        $this->channel->basic_publish($message, '', self::RABBITMQ_QUEUE_NAME);
    }

    public function close()
    {
        $this->channel->close();
        $this->connection->close();
    }
}
